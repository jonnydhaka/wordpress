jQuery(document).ready(function ($) {
		function clearColor(){
			$('body').removeClass('color-green color-blue color-violet color-yellow')
		}
		$('body').prepend('<div class="demo-rtl"><a class="rtldemo" href="?d=rtl" target="_blank">RTL</a></div><div class="demo-ltr"><a class="ltrdemo" href="?d=ltr" target="_blank">LTR</a></div>')
		
		$(document).on('click','.tools a', function(e){
			e.preventDefault();
			var $this = $(this);
			if ($this.hasClass('color-green')){
				clearColor();
				$('body').addClass('color-green')
			}
			if ($this.hasClass('color-blue')){
				clearColor();
				$('body').addClass('color-blue')
			}
			if ($this.hasClass('color-violet')){
				clearColor();
				$('body').addClass('color-violet')
			}
			if ($this.hasClass('color-yellow')){
				clearColor();
				$('body').addClass('color-yellow')
			}
		})
	
	})