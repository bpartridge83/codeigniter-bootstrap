<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class MY_Pagination extends CI_Pagination {

    /*
    <div class="pagination pagination-right">
    <ul>
        <li><a href="#">Prev</a></li>
        <li class="active">
            <a href="#">1</a>
        </li>
        <li><a href="#">2</a></li>
        <li><a href="#">3</a></li>
        <li><a href="#">4</a></li>
        <li><a href="#">Next</a></li>
    </ul>
    </div>
    */

    public function __construct(){
        parent::__construct();
        
        $this->enable_query_strings = true;
        $this->query_string_segment = 'page';
        $this->page_query_string = true;
        
        $this->cur_page = 1;
        
        $this->num_links = 3;
        
        $this->per_page = 20;
        
        $this->next_link = 'Next &nbsp;&nbsp;&rsaquo;';
        $this->prev_link = '&lsaquo;&nbsp;&nbsp; Previous';
        
        $this->prev_tag_open = $this->next_tag_open = $this->num_tag_open = '<li>';
        $this->prev_tag_close = $this->next_tag_close = $this->num_tag_close = '</li>';
        
        $this->cur_tag_open = '<li class="active"><a href="#">';
        $this->cur_tag_close = '</a></li>';
        
        /*
        $this->prev_tag_close = '</li>';
        $this->next_tag_open = '<li>';
        $this->next_tag_close = '</li>';
        $this->num_tag_open = '<li>';
        $this->num_tag_close = '</li>';
        */
        
        $this->first_link = false;
        $this->last_link = false;
        
        $this->full_tag_open = '<ul>';
        $this->full_tag_close = '</ul>';
    }
    
    public function config()
    {
        return array(
            'base_url' => $this->base_url,
            'per_page' => $this->per_page,
            'enable_query_strings' => $this->enable_query_strings,
            'page_query_string' => $this->enable_query_strings,
            'query_string_segment' => $this->query_string_segment,
            'total_rows' => $this->total_rows,
            'per_page' => $this->per_page,
            'prev_tag_open' => $this->prev_tag_open,
            'prev_tag_close' => $this->prev_tag_close,
            'next_tag_open' => $this->next_tag_open,
            'next_tag_close' => $this->next_tag_close,
            'num_tag_open' => $this->num_tag_open,
            'num_tag_close' => $this->num_tag_close,
            'first_link' => $this->first_link,
            'last_link' => $this->last_link,
            'full_tag_open' => $this->full_tag_open,
            'full_tag_close' => $this->full_tag_close,
        );
    }
    
    public function create($base_url, $total_rows, $per_page = null, $num_links = null)
    {
        $this->base_url = $base_url;
        $this->total_rows = $total_rows;
        
        //$this->cur_page = (isset($_GET['page'])) ? $_GET['page'] : 1;
        
        if ($per_page) {
            $this->per_page = $per_page;
        }
        
        if ($num_links) {
            $this->num_links = $num_links;
        }
    
        $this->initialize($this->config());
    }
    
    public function render($class = null)
    {
        $response = $this->create_links();
        
        $pag_tag_open = sprintf('<div class="pagination %s">', $class);
        $pag_tag_close = '</div>';
        
        return sprintf('%s%s%s', $pag_tag_open, $response, $pag_tag_close);
    }
    
    /**
	 * Generate the pagination links
	 *
	 * @access	public
	 * @return	string
	 */
	function create_links()
	{
		// If our item count or per-page total is zero there is no need to continue.
		if ($this->total_rows == 0 OR $this->per_page == 0)
		{
			return '';
		}

		// Calculate the total number of pages
		$num_pages = ceil($this->total_rows / $this->per_page);

		// Is there only one page? Hm... nothing more to do here then.
		if ($num_pages == 1)
		{
			return '';
		}

		// Determine the current page number.
		$CI =& get_instance();

		if ($CI->config->item('enable_query_strings') === TRUE OR $this->page_query_string === TRUE)
		{
			if ($CI->input->get($this->query_string_segment) != 0)
			{
				$this->cur_page = $CI->input->get($this->query_string_segment);

				// Prep the current page - no funny business!
				$this->cur_page = (int) $this->cur_page;
			}
		}
		else
		{
			if ($CI->uri->segment($this->uri_segment) != 0)
			{
				$this->cur_page = $CI->uri->segment($this->uri_segment);

				// Prep the current page - no funny business!
				$this->cur_page = (int) $this->cur_page;
			}
		}

		$this->num_links = (int) $this->num_links;

		if ($this->num_links < 1)
		{
			show_error('Your number of links must be a positive number.');
		}

		if ( ! is_numeric($this->cur_page))
		{
			$this->cur_page = 1;
		}

		// Is the page number beyond the result range?
		// If so we show the last page
		if ($this->cur_page > $this->total_rows)
		{
			$this->cur_page = ($num_pages - 1) * $this->per_page;
		}

		$uri_page_number = $this->cur_page;
		$this->cur_page = ($this->cur_page - 1) * $this->per_page;
		$this->cur_page = floor(($this->cur_page/$this->per_page) + 1);

		// Calculate the start and end numbers. These determine
		// which number to start and end the digit links with
		$start = (($this->cur_page - $this->num_links) > 0) ? $this->cur_page - ($this->num_links - 1) : 1;
		$end   = (($this->cur_page + $this->num_links) < $num_pages) ? $this->cur_page + $this->num_links : $num_pages;

		// Is pagination being used over GET or POST?  If get, add a per_page query
		// string. If post, add a trailing slash to the base URL if needed
		if ($CI->config->item('enable_query_strings') === TRUE OR $this->page_query_string === TRUE)
		{		
            if (strpos($this->base_url, '?') && !$this->cur_page == 1) {
                $append = '&amp;';
            } else {
                $append = '?';
            }
            
			$this->output_url = rtrim($this->base_url).$append.$this->query_string_segment.'=';
		}
		else
		{
			$this->output_url = rtrim($this->base_url, '/') .'/';
		}

		// And here we go...
		$output = '';

		// Render the "First" link
		if  ($this->first_link !== FALSE AND $this->cur_page > ($this->num_links + 1))
		{
			$first_url = ($this->first_url == '') ? $this->output_url : $this->first_url;
			$output .= $this->first_tag_open.'<a '.$this->anchor_class.'href="'.$first_url.'">'.$this->first_link.'</a>'.$this->first_tag_close;
		}

		// Render the "previous" link
		if  ($this->prev_link !== FALSE AND $this->cur_page != 1)
		{
			$i = $uri_page_number - 1;

			if ($i == 0 && $this->first_url != '')
			{
				$output .= $this->prev_tag_open.'<a '.$this->anchor_class.'href="'.$this->first_url.'">'.$this->prev_link.'</a>'.$this->prev_tag_close;
			}
			else
			{
				$i = ($i == 0) ? '' : $this->prefix.$i.$this->suffix;
				$output .= $this->prev_tag_open.'<a '.$this->anchor_class.'href="'.$this->output_url.$i.'">'.$this->prev_link.'</a>'.$this->prev_tag_close;
			}

		}

		// Render the pages
		if ($this->display_pages !== FALSE)
		{
			// Write the digit links
			for ($loop = $start -1; $loop <= $end; $loop++)
			{
				//$i = ($loop * $this->per_page) - $this->per_page;

                $i = $loop;

                //$i = $loop * $this->per_page;

				if ($i > 0)
				{
					if ($this->cur_page == $loop)
					{
						$output .= $this->cur_tag_open.$loop.$this->cur_tag_close; // Current page
					}
					else
					{
						$n = ($i == 0) ? '' : $i;

						if ($n == '' && $this->first_url != '')
						{
							$output .= $this->num_tag_open.'<a '.$this->anchor_class.'href="'.$this->first_url.'">'.$loop.'</a>'.$this->num_tag_close;
						}
						else
						{
							$n = ($n == '') ? '' : $this->prefix.$n.$this->suffix;

							$output .= $this->num_tag_open.'<a '.$this->anchor_class.'href="'.$this->output_url.$n.'">'.$loop.'</a>'.$this->num_tag_close;
						}
					}
				}
			}
		}

		// Render the "next" link
		if ($this->next_link !== FALSE AND $this->cur_page < $num_pages)
		{
			$output .= $this->next_tag_open.'<a '.$this->anchor_class.'href="'.$this->output_url.$this->prefix.($this->cur_page + 1).$this->suffix.'">'.$this->next_link.'</a>'.$this->next_tag_close;
		}

		// Render the "Last" link
		if ($this->last_link !== FALSE AND ($this->cur_page + $this->num_links) < $num_pages)
		{
			$i = (($num_pages * $this->per_page) - $this->per_page);
			$output .= $this->last_tag_open.'<a '.$this->anchor_class.'href="'.$this->output_url.$this->prefix.$i.$this->suffix.'">'.$this->last_link.'</a>'.$this->last_tag_close;
		}

		// Kill double slashes.  Note: Sometimes we can end up with a double slash
		// in the penultimate link so we'll kill all double slashes.
		$output = preg_replace("#([^:])//+#", "\\1/", $output);

		// Add the wrapper HTML if exists
		$output = $this->full_tag_open.$output.$this->full_tag_close;

		return $output;
	}

    
}
