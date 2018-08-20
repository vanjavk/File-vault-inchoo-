<?php

class Bootstrap {

	private $_url;
	private $_controller = NULL;
    private $_defaultController;

    public function __construct(){
        Session::init();
    }

	public function setController($name)
    {
		$this->_defaultController = $name; 
	}

    public function setTemplate($template)
    {
       Session::set('template',$template);
    }

	public function init()
    {
		$this->_getUrl();		

		if(empty($this->_url[0])){
			$this->_loadDefaultController();
			return false;
		}

		$this->_loadExistingController();
		$this->_callControllerMethod();
	}

	protected function _getUrl()
    {
		$url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : NULL;
		$url = filter_var($url, FILTER_SANITIZE_URL);
		$this->_url = explode('/',$url);
	}

	protected function _loadDefaultController()
    {
		require 'controllers/'.$this->_defaultController.'.php';
		$this->_controller = new $this->_defaultController();
		$this->_controller->loadModel($this->_defaultController);
		$this->_controller->index();
	}

	protected function _loadExistingController()
    {

		$file = 'controllers/'.$this->_url[0].'.php';

		if(file_exists($file)){
			require $file;
			$this->_controller = new $this->_url[0];

			$this->_controller->loadModel($this->_url[0]);

		} else {
			$this->_error("File does not exist: ".$this->_url[0]);
			return false;
		}

	}

    protected function _callControllerMethod()
    {
        $length = count($this->_url);
        if ($length > 1) {
            if (!method_exists($this->_controller, $this->_url[1])) {
                $this->_error("Method does not exist: ".$this->_url[1]);
                return false;
            }
        }
    
        switch ($length) {
            case 5:
                $this->_controller->{$this->_url[1]}($this->_url[2], $this->_url[3], $this->_url[4]);
                break;
            case 4:
                $this->_controller->{$this->_url[1]}($this->_url[2], $this->_url[3]);
                break;
            case 3:
                $this->_controller->{$this->_url[1]}($this->_url[2]);
                break;
            case 2:
                $this->_controller->{$this->_url[1]}();
                break;
            
            default:
                $this->_controller->index();
                break;
        }
    }

    protected function _error($error) 
    {


        require 'controllers/'.$this->_defaultController.'.php';
        $this->_controller = new $this->_defaultController();
        $this->_controller->loadModel($this->_defaultController);
        $this->_controller->error();  
        die;
    }



}