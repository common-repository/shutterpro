<?php
/*
Plugin Name: ShutterPro
Plugin URI: http://www.shutterpro.co.uk/uploaders/
Description: Show your ShutterPro cloud photographs live on your own web site.
Version: 1.0.0
Author: Tier One Design
Author URI: http://www.tieronedesign.co.uk/

*/

add_action('wp_enqueue_scripts', 'sp_add_main_stylesheet');

add_shortcode('shutterpro', 'sp_display_photos');

function sp_add_main_stylesheet()
{
    wp_register_style('prefix-style', plugins_url('style.css', __FILE__));
    wp_enqueue_style('prefix-style');
}

function sp_display_photos($atts)
{
	$content = '';
	
	if(isset($atts['limit']))
	{
		$limit_results = true;
		$total_results = $atts['limit'];
	}
	else
	{
		$limit_results = false;
	}
	
	if(isset($atts['set']))
	{
		$set_data = json_decode(file_get_contents('http://www.shutterpro.co.uk/api/public_set?set='. $atts['set']));
		
		$i = 0;
		
		$content .= '<ul id="shutterpro">';
		
		$list = (array)$set_data->items;
		shuffle($list);
		
		foreach($list as &$item)
		{
			$item_visible = true;
			
			if($limit_results==true)
			{
				if($i > ($total_results - 1))
				{
					$item_visible = false;
				}
			}
			
			if($item_visible==true)
			{
				$content .= '<li>';
				
					if(isset($atts['link']))
					{
						if($atts['link']=='preview')
						{
							if(isset($atts['class']))
							{
								$content .= '<a href="'. $item->preview .'" class="'. $atts['class'] .'">';
							}
							else
							{
								$content .= '<a href="'. $item->preview .'">';
							}
						}
						elseif($atts['link']=='original')
						{
							if(isset($atts['class']))
							{
								$content .= '<a href="'. $item->original .'" class="'. $atts['class'] .'">';
							}
							else
							{
								$content .= '<a href="'. $item->original .'">';
							}
						}
						else
						{
							if(isset($atts['class']))
							{
								$content .= '<a href="http://www.shutterpro.co.uk/'. $item->username .'/item/'. $item->id .'" target="_blank" class="'. $atts['class'] .'">';
							}
							else
							{
								$content .= '<a href="http://www.shutterpro.co.uk/'. $item->username .'/item/'. $item->id .'" target="_blank">';
							}
						}
					}
					else
					{
						if(isset($atts['class']))
						{
							$content .= '<a href="http://www.shutterpro.co.uk/'. $item->username .'/item/'. $item->id .'" target="_blank" class="'. $atts['class'] .'">';
						}
						else
						{
							$content .= '<a href="http://www.shutterpro.co.uk/'. $item->username .'/item/'. $item->id .'" target="_blank">';
						}
					}
					
					$content .= '<img src="'. $item->thumbnail .'" alt="'. $item->name .'" border="0" alt="'. $item->name .'" />';
					$content .= '</a>';
				$content .= '</li>';
			}
			
			$i++;
		}
		
		$content .= '</ul>';
		$content .= '<div class="clear"></div>';
	}
	
	return $content;
}
?>