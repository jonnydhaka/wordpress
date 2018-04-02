<?php
class themename_Banner  extends WPBakeryShortCode{

    public function __construct()
    {
        add_shortcode( 'themename_banner', array($this, 'themename_banner_func'));
    }

   public function themename_banner_func ($atts, $content = null){
        extract(shortcode_atts(array(
            'heading1_list' => '',
            'heading2_list' => '',
            'heading3_list' => '',
            'heading_1' => '',
            'heading_2' => '',
			'heading_3' => ''
        ), $atts));
		$valuesSocial = vc_param_group_parse_atts($heading1_list);
		$valuesSpacial = vc_param_group_parse_atts($heading2_list);
		$valuesAttractions = vc_param_group_parse_atts($heading3_list);
		ob_start();
?>

<div class="expand-btns-wrap d-none d-md-block">
  <div class="container d-flex flex-column align-items-end h-100">
    <div class="expand-btn expand-btn--social" id="expBut1"> 
    	<a href="#" class="expand-btn-first">
      		<div><?php echo $heading_1 ; ?></div>
      	</a>
      <?php 
		foreach($valuesSocial as $social){
			$link_href='javascript:void(0)';
			$link_target='';
			$icon='';
			$title='';
			if(isset($social['heading1_bestelicons'])){
				$icon=$social['heading1_bestelicons'];
			}
			if(isset($social['heading1_icon'])){
				$icon = $social['heading1_icon'];
			}
			if(isset($social['heading1_title'])){
				$title=$social['heading1_title'];
			}
			if(isset($social['heading1_call_action'])){
				$social_link=vc_build_link( $social['heading1_call_action']);
				$link_href=$social_link['url'];
				$link_target=$social_link['target'];
			}
		?>
		<a href="<?php echo $link_href ?>" class="link-<?php echo strtolower($title) ;?>" target="<?php echo $link_target?>"> <i class="<?php echo $icon ?>" data-toggle="tooltip" data-placement="top" title="" data-original-title="<?php echo $title ?>"></i> </a>
	<?php } ?>
    </div>
    <div class="expand-btn expand-btn--offer" id="expBut2"> 
    	<a href="#" class="expand-btn-first">
      		<div><?php echo $heading_2 ; ?></div>
      	</a>
	<?php 
		foreach($valuesSpacial as $Spacial){
			$link_href='javascript:void(0)';
			$link_target='';
			$image_url='';
			$title='';
			$title1='';
			if(isset($Spacial['heading2_title'])){
				$title=$Spacial['heading2_title'];
			}
			if(isset($Spacial['heading2_title1'])){
				$title1=$Spacial['heading2_title1'];
			}
			if(isset($Spacial['heading2_img'])){
				$image_url = wp_get_attachment_url($Spacial['heading2_img']);
			}
			if(isset($Spacial['heading2_call_action'])){
				$Spacial_link=vc_build_link( $Spacial['heading2_call_action']);
				$link_href=$Spacial_link['url'];
				$link_target=$Spacial_link['target'];
			}
			
		?>
      <a href="<?php echo $link_href ?>" target="<?php echo $link_target ?>"> <img src="<?php echo $image_url ?>" alt="<?php echo $title ?>">
      	<div class="txt-wrap"> <span class="txt-1"><span><?php echo $title ?></span> <span class="theme-color"><?php echo $title1 ?></span></span> </div>
      </a>
      <?php } ?>
    </div>
    <div class="expand-btn" id="expBut3"> 
    	<a href="#" class="expand-btn-first">
      		<div><?php echo $heading_3 ; ?></div>
      	</a>
	<?php 
		foreach($valuesAttractions as $Attractions){
			$link_href='javascript:void(0)';
			$link_target='';
			$image_url='';
			$title='';
			$title1='';
			$icon='';
			if(isset($Attractions['heading3_title'])){
				$title=$Attractions['heading3_title'];
			}
			if(isset($Attractions['heading3_title1'])){
				$title1=$Attractions['heading3_title1'];
			}
			if(isset($Attractions['heading3_bestelicons'])){
				$icon = $Attractions['heading3_bestelicons'];
			}
			if(isset($Attractions['heading3_icon'])){
				$icon = $Attractions['heading3_icon'];
			}
			if(isset($Attractions['heading3_call_action'])){
				$Attractions_link=vc_build_link( $Attractions['heading3_call_action']);
				$link_href=$Attractions_link['url'];
				$link_target=$Attractions_link['target'];
			}
		?>
      <a href="<?php echo $link_href ?>" target="<?php echo $link_target ?>"> <i class="<?php echo $icon ?>"></i>
      	<div><?php echo $title ?></div>
      	<div class="txt-sm"><?php echo $title1 ?></div>
      </a>
      <?php } ?>
    </div>
  </div>
</div>
<?php
      $output = ob_get_clean();
      return $output;
    }
}

new themename_banner();
