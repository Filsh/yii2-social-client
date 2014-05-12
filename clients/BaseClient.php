<?php

namespace filsh\yii2\social\clients;

use yii\base\Component;

abstract class BaseClient extends Component implements ClientInterface
{
    /**
     * @var string auth service id.
     * This value mainly used as HTTP request parameter.
     */
    private $_id;
    
    /**
     * @var string auth service name.
     * This value may be used in database records, CSS files and so on.
     */
    private $_name;
    
    /**
     * @var string auth service title to display in views.
     */
    private $_title;
    
    /**
     * @var string client ID.
     */
    public $clientId;
    
    /**
     * @var string client secret.
     */
    public $clientSecret;
    
    /**
     * @param string $id service id.
     */
    public function setId($id)
    {
        $this->_id = $id;
    }

    /**
     * @return string service id
     */
    public function getId()
    {
        if (empty($this->_id)) {
            $this->_id = $this->getName();
        }

        return $this->_id;
    }

    /**
     * @param string $name service name.
     */
    public function setName($name)
    {
        $this->_name = $name;
    }

    /**
     * @return string service name.
     */
    public function getName()
    {
        if ($this->_name === null) {
            $this->_name = $this->defaultName();
        }

        return $this->_name;
    }

    /**
     * @param string $title service title.
     */
    public function setTitle($title)
    {
        $this->_title = $title;
    }

    /**
     * @return string service title.
     */
    public function getTitle()
    {
        if ($this->_title === null) {
            $this->_title = $this->defaultTitle();
        }

        return $this->_title;
    }
}