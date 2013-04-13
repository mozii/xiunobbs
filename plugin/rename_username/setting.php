<?php

$error = $input = array();
if(!$this->form_submit()) {
	$this->view->assign('dir', $dir);
	$this->view->display('plugin_rename_username.htm');
} else {
	$username = core::gpc('username', 'P');
	$newusername = core::gpc('newusername', 'P');
	
	if(empty($username)) {
		$this->message('请填写需要修改的用户名。');
	}
	if(empty($newusername)) {
		$this->message('请填写修改成的用户名。');
	}
	if($username == $newusername) {
		$this->message('用户名不能相等。');
	}
	
	$error = $this->user->check_username($username);
	$error && $this->message($error);
	$user = $this->user->get_user_by_username($username);
	$this->check_user_exists($user);
	
	$error = $this->user->check_username($newusername);
	$error && $this->message($error);
	$error = $this->user->check_username_exists($newusername);
	$error && $this->message($error);
	
	$username = $user['username'];
	
	$this->user->index_update(array('username'=>$username), array('username'=>$newusername), TRUE);
	$this->online->index_update(array('username'=>$username), array('username'=>$newusername), TRUE);
	$this->modlog->index_update(array('username'=>$username), array('username'=>$newusername), TRUE);
	$this->rate->index_update(array('username'=>$username), array('username'=>$newusername), TRUE);
	$this->pm->index_update(array('username1'=>$username), array('username1'=>$newusername), TRUE);
	$this->pm->index_update(array('username2'=>$username), array('username2'=>$newusername), TRUE);
	if($user['posts'] > 0) {
		$this->post->index_update(array('username'=>$username), array('username'=>$newusername), TRUE);
	}
	if($user['threads'] > 0) {
		$this->thread->index_update(array('username'=>$username, 'lastuid'=>0, 'lastusername'=>''), array('username'=>$newusername), TRUE);
	}
	
	$this->message('恭喜，修改成功。', 1, "?plugin-setting-dir-$dir.htm");
}



?>