<?php
/*----------------------------------------------------------------------------
 	        �2006 ALEXANDER STREET PRESS LLC. ALL RIGHTS RESERVED.
------------------------------------------------------------------------------
                     Alexander Street Press LLC | $Revision$
    3212 Duke Street, Alexandria, VA 22314, USA | $Date$
                http://www.alexanderstreet.com  | $Author$
------------------------------------------------------------------------------*/

/**
* DHTML Progress Indicator
*
* This class provides an efficient way of keeping the user updated with a
* DHTML rendered progress meter. The class only outputs small javascript
* fragments for every required update.
*
* Example usage
* @code
*   $progress=new Widget_ProgressIndicator;
*   $progress->showBar();
*   $progress->setText("processing foo");
*   $progress->setRange(50,100);
*   for ($x=50; $<100; $x++)
*   {
*      $progress->setProgress($x);
*   }
* @endcode
*
* For many large operations, you might share a single progress meter
* between many tasks. Rather than have the meter go from 0 to 100% with
* every tasks, you can tell the meter it will be handling a number of
* sub tasks with different weights, e.g.
*
*
* @code
*   $progress=new Widget_ProgressIndicator;
*   $progress->showBar();
*
*   $progress->setSubtasks(array(2,4));
*
*   $progress->setCurrentSubTask(1);
*   $progress->setText("processing foo");
*   $progress->setRange(50,100);
*   for ($x=50; $<100; $x++)
*   {
*      $progress->setProgress($x);
*   }
*
*   $progress->setCurrentSubTask(2);
*   $progress->setText("processing bar");
*   $progress->setRange(50,100);
*   for ($x=50; $<100; $x++)
*   {
*      $progress->setProgress($x);
*   }
*
* @endcode
*
* In the above example, we give task 1 a weight of '2' and task 2
* a weight of '4', suggesting that task1 will take 33% of the time
* and task 2 the remainder. If tasks are broadly similar, you can
* just use a weight of 1 for each task.
*
* To reset subtasks, just pass 0 to setSubtasks
*
* @code
*   $progress->setSubtasks(0);
* @endcode
*
*
* @ingroup Widgets
*/
class Widget_ProgressIndicator
{
	private $_min=0;
	private $_max=100;
	private $_cur=0;
	protected $_text="Progress";
	private $_maxw=300;
	private $_maxh=16;
	private $_curw=0;
	private $_tstart=0;
	protected $_tend=0;
	protected $_percent=0;
	private $_remain=0;
	private $_output_bar=0;

	private $_taskweights;
	private $_task=0;
	
	protected $_lastOutput=0;

	/**
	 * constructor
	 */
	public function Widget_ProgressIndicator()
	{
		//init start time, but we really need to do
		//this every time the range is changed
		$this->_tstart=time();
	}

	/**
	 * outputs bar onto page ready for updates
	 *
	 * Note that if showBar is NOT called, then the progress indicator will not produce any
	 * visual output. This might be useful for handing a progress indicator to something
	 * which expects one, but where you are running in a context which does not warrant a
	 * visual element
	 */
	public function showBar()
	{
		if ($this->_output_bar)
		{
			//can only use this to output a single progress meter
			return;
		}

		$wouter=$this->_maxw+2;
		$houter=$this->_maxh+2;
		echo "<div style='width:{$wouter}px;padding:2px;border: 1px black solid;'>";

		$w=$this->_PosToWidth($this->_cur);

		echo "<div style='width:{$wouter}px;height:{$houter}px;padding:1px;background:silver;border 1px black solid;'>";
			echo "<div style='background:red; width:{$w}px; height:{$this->_maxh}px;' id='progressbar'>";
			echo "</div>";
		echo "</div>";

			echo "<div style='font-family:Arial;font-size:8pt;text-align:center;' id='progresstext'>{$this->_text}</div>";

		echo "</div>\n";

		echo "<script language=\"Javascript\">\n";

		echo "bar=document.getElementById('progressbar');\n";
		echo "text=document.getElementById('progresstext');\n";

		echo "</script>\n";

		flush();

		$this->_output_bar=1;
	}

	/**
	 * Set the weights for subtasks
	 *
	 * This is used in conjunction with setCurrentSubtask to enable large operation
	 * be broken into separate tasks but with the progress meter moving from empty to
	 * complete smoothly.
	 *
	 * @param[in] $weights an array of relative task weights
	 */
	public function setSubtasks($weights)
	{
		if (is_array($weights))
		{
			//normalise weights so they add up to 1
			$total=0;
			foreach($weights as $weight)
			{
				$total+=$weight;
			}

			$offset=0;
			$this->_taskweights=array();
			foreach($weights as $weight)
			{
				$w=array();
				$w['offset']=$offset;
				$w['weight']=$weight/$total;
				$offset+=$w['weight'];
				$this->_taskweights[]=$w;
			}

			$this->_task=1;
		}
		else
		{
			$this->_taskweights=array();
			$this->_task=0;
		}
	}

	/**
	 * Set current subtask
	 *
	 * @param[in] $subtask 1-based task index
	 */
	public function setCurrentSubtask($subtask)
	{
		if ($subtask>0 and $subtask<=count($this->_taskweights))
		{
			$this->_task=$subtask;
		}
		else
		{
			trigger_error("Invalid subtask index '$subtask' - ".count($this->_taskweights)." tasks defined");
		}
	}

	/**
	 * Updates bar text
	 *
	 * @param[in] $text new progress bar status text
	 */
	public function setText($text=null)
	{
		if ($text)
        {
            $this->_text=$text;
        }
        else
        {
            $text = $this->_text;
        }


		if($this->_tend)
		{
			if ($this->_remain>3600*24)
			{
				//more than a day to go
				$text.=" [eta ".round($this->_remain/(3600*24),2)." days on ".strftime("%a %d %b at %H:%M", $this->_tend)."]";
			}
			else
			{
				//less than a day
				$text.=" [eta ".round($this->_remain/60)." mins at ".strftime("%H:%M", $this->_tend)."]";
			}
		}

		if ($this->_output_bar)
		{
			echo "<script language=\"Javascript\">\n";
			echo "text.innerHTML='".addslashes($text)."';\n";
			echo "</script>\n";
			flush();
			$this->_lastOutput=time();
		}
	}

	/**
	 * Set the range of values, used to interpret the curret position
	 * as a percentage completion
	 * @param[in] $min lower point of progress range
	 * @param[in] $max upper point of progress range
	 */
	public function setRange($min, $max)
	{
		$this->_min=$min;
		$this->_max=$max;

		//don't reset timings if using subtask
		if (count($this->_taskweights)<=1)
		{
			$this->_tstart=time();
			$this->_tend=0;
			$this->_remain=0;
		}
	}

	/**
	 * Get current completion percentage
	 */
	private function _getPercentage($pos)
	{
		if ($this->_min==$this->_max)
		{
			return 0;
		}
		else
		{
			$percentage=($pos-$this->_min)/($this->_max-$this->_min);
			if ($this->_task>0)
			{
				//scale percentage for subtask
				$w=$this->_taskweights[$this->_task-1];

				$percentage= $w['offset'] + ($percentage * $w['weight']);
			}

			return $percentage;
		}
	}

	/**
	 * Figure out how many pixels a given position is at
	 */
	private function _PosToWidth($pos)
	{
		$percentage=$this->_getPercentage($pos);
		return ceil($this->_maxw*$percentage);
	}

	/**
	 * Set current progress
	 *
	 * If the new position requires a visual update, this function outputs
	 * the necessary javascript to accomplish it
	 * @param[in] $pos current position, should be in range set by setRange
	 */
	public function setProgress($pos)
	{
		//range limit
		$pos=min(max($pos, $this->_min), $this->_max);

		//if we've output the progress bar, and position is
		//different, update it!
		if ($this->_output_bar && ($this->_cur!=$pos))
		{
			$neww=$this->_PosToWidth($pos);
			if ($neww!=$this->_curw)
			{
				echo "<script language=\"Javascript\">\n";
				echo "bar.style.width='{$neww}px';\n";
				echo "</script>\n";
				flush();
				$this->_curw=$neww;
				$this->_lastOutput=time();
			}
		}

		//store current position
		$this->_cur=$pos;

		//figure out eta
		$duration=time()-$this->_tstart;


		$this->_percent=$this->_getPercentage($pos);
		if (($this->_percent>0.05) ||
		    ($this->_percent>0 && $duration>30 ))
		{
			$estimate_total=$duration/$this->_percent;
			$tend=$this->_tstart+$estimate_total;
			$remain=$estimate_total-$duration;

			//has remainder changed by one minute or more?
			if (round($this->_remain/60)!=round($remain/60))
			{
				$this->_tend=$tend;
				$this->_remain=$tend-time();

				//update text
				$this->SetText();
			}
		}

		//here we make sure we send *something* every 30 seconds just to
		//keep the connection active - seems to help for long running
		//processes
		$timeSinceOutput=time() - $this->_lastOutput;
		if (($timeSinceOutput>30) &&  ($this->_output_bar))
		{
			echo "<!-- ping -->\n";
			flush();
			$this->_lastOutput=time();
			
		}
	}


}
?>