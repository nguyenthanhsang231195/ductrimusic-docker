<?php
class maximg{
	private $w;
	private $h;
	private $img;
	private $tmp_img;
	private $img_type;
	
	public function load_img($filename){
		$image_info = getimagesize($filename);
		$this->img_type = $image_info[2];
		$this->w = $image_info[0];
		$this->h = $image_info[1];
		if($this->img_type == IMAGETYPE_PNG){
			//header('Content-Type: image/png');
			$this->img = imagecreatefrompng($filename);
			imagesavealpha($this->img, true);
		}
		else if($this->img_type == IMAGETYPE_JPEG){
			$this->img = imagecreatefromjpeg($filename);
		}
	}
	
	private function create_empty_img($width,$height){
		$this->tmp_img = imagecreatetruecolor($width, $height);
		imagesavealpha($this->tmp_img, true);
		$color = imagecolorallocatealpha($this->tmp_img, 0, 0, 0, 127);
		imagefill($this->tmp_img, 0, 0, $color);
		return $this->tmp_img;
	
	}
	
	public function resize($width,$height){
		$this->create_empty_img($width,$height);
		imagecopyresampled($this->tmp_img,$this->img,0,0,0,0,$width,$height,$this->w,$this->h);
		$this->w = $width;
		$this->h =$height;
		imagedestroy($this->img);
		$this->img=$this->tmp_img;
	}
	
	private function private_resizer($img,$path,$width,$height){
		$image_info = getimagesize($path);
		$this->create_empty_img($width,$height);
		imagecopyresampled($this->tmp_img,$img,0,0,0,0,$width,$height,$image_info[0],$image_info[1]);
		$img = $this->tmp_img;
		return $img;	
	}
	
	public function apply_mask($mask_path){
		//header('Content-Type: image/png');		
		$mask_img = imagecreatefrompng($mask_path);
		$mask_img = $this->private_resizer($mask_img,$mask_path,$this->w,$this->h);
		$dest_img = $this->img;
		$this->create_empty_img($this->w,$this->h);
		
		for($y=0;$y<$this->h;$y++){
			for($x=0;$x<$this->w;$x++){	
				$opacity =0;
				$pixel_info_mask = imagecolorat($mask_img , $x, $y);
				$r_m = ($pixel_info_mask >> 16) & 0xFF;
				$g_m = ($pixel_info_mask >> 8) & 0xFF;
				$b_m = $pixel_info_mask & 0xFF;
				$rgb =  imagecolorat($dest_img, $x, $y);
				$pixel_info_dest = imagecolorsforindex($this->img, $rgb);
				/*$r_d = ($pixel_info_dest >> 16) & 0xFF;
				$g_d = ($pixel_info_dest >> 8) & 0xFF;
				$b_d = $pixel_info_dest & 0xFF;*/
				$r_d = $pixel_info_dest["red"];
				$g_d = $pixel_info_dest["green"];
				$b_d = $pixel_info_dest["blue"];
				$a_d = $pixel_info_dest["alpha"];
				
				if($b_m==0){
					/*$opacity = 127;
					imagesetpixel($this->tmp_img , $x,$y, imagecolorallocatealpha ( $dest_img , $r_d, $g_d , $b_d, $opacity  ));*/
				}
				else if ($b_m==255){
					imagesetpixel($this->tmp_img , $x,$y, imagecolorallocatealpha ( $dest_img , $r_d, $g_d , $b_d, $a_d ));
				}
				else{
					$opacity = 127 - round(($b_m*127)/255); 
					if($a_d == 0){
						imagesetpixel($this->tmp_img , $x,$y, imagecolorallocatealpha ( $dest_img , $r_d, $g_d , $b_d, $opacity ));
						//var_dump($a_d);
					}
					else{
						imagesetpixel($this->tmp_img , $x,$y, imagecolorallocatealpha ( $dest_img , $r_d, $g_d , $b_d, $a_d ));
					}
				}	
			}
		}
		
		$this->img = $this->tmp_img;
		
	}
	
	public function black_white(){
		$img = $this->create_empty_img($this->w,$this->h);
		for($y=0;$y<$this->h;$y++){
			for($x=0;$x<$this->w;$x++){	
				$rgb =  imagecolorat($this->img, $x, $y);
				$pixel_info_dest = imagecolorsforindex($this->img, $rgb);
				$r = $pixel_info_dest["red"];
				$g = $pixel_info_dest["green"];
				$b = $pixel_info_dest["blue"];
				$a = $pixel_info_dest["alpha"];	
				$b_w = ($r +$g +$b)/3;
				imagesetpixel($img , $x,$y, imagecolorallocatealpha ( $this->img , $b_w+5, $b_w+5 , $b_w+5, $a ));			
			}
		}
		$this->img = $img;
		
	}
	
	private function get_mid($a,$b,$c){
		$mid;
		$max  = max($a,$b,$c);
		$min = min($a,$b,$c);
		if(($a!=$max)&&($a!=$min)){return $mid=$a;}
		else if(($b!=$max)&&($b!=$min)){return $mid=$b;}
		else if(($c!=$max)&&($c!=$min)){return $mid=$c;}
	}
	
	public function hue(){
		$img = $this->create_empty_img($this->w,$this->h);
		for($y=0;$y<$this->h;$y++){
			for($x=0;$x<$this->w;$x++){	
				$rgb =  imagecolorat($this->img, $x, $y);
				$pixel_info_dest = imagecolorsforindex($this->img, $rgb);
				$r = $pixel_info_dest["red"];
				$g = $pixel_info_dest["green"];
				$b = $pixel_info_dest["blue"];
				$a = $pixel_info_dest["alpha"];	
				$M  = max($r,$g,$b);
				$m = min($r,$g,$b);
				$C = $M - $m;
				$mid = $this->get_mid($r,$g,$b);
				
				if($C==0){}
				else if($r == $M){
					$H= (($g-$b)/$C) ;
					echo $H1."<br>";
					//imagesetpixel($img , $x,$y, imagecolorallocatealpha ( $this->img , $b_w+5, $b_w+5 , $b_w+5, $a ));	
				}
			}
		}
		$this->img = $img;
	}
	
	public function output(){
		imagepng($this->img);
	}
	
	public function export($path){
		imagepng($this->img,$path);
	}
}

/*
$obj = new maximg;
$obj->load_img("src/poro.png");

// Doi kich thuoc
$obj->resize(300,300);
// Ap dung mat ma
$obj->apply_mask("src/heart.png");

// Doi mau
$obj->hue();
$obj->black_white();

// View result
$obj->output();
$obj->export("src/compos1.png");
*/
?>