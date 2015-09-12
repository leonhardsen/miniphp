<?php

class homeController{

	public function welcome($params){		
		include('app/view/welcome/welcome.html');
	}

	public function page($params){
		include('app/view/welcome/page.html');
	}

}

?>