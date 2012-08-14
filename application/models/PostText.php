<?php
/**
 * Created by IntelliJ IDEA.
 * User: vladvidican
 * Date: 7/26/12
 * Time: 4:45 PM
 * To change this template use File | Settings | File Templates.
 */
class Model_PostText
{
    private $id;
    private $postId;
    private $text;
    private $order;
    private $column;

    function __construct(array $options = null)
    {
        if (is_array($options))
            $this->setOptions($options);
    }

    /**
     *
     * Set values to object's properties
     * @param array $options
     */
    public function setOptions(array $options)
    {
        // get methods
        $methods = get_class_methods($this);
        // fill properties
        foreach ($options as $key => $value) {
            // format option name
            $optionName = explode('_', $key);
            foreach ($optionName as &$name)
            {
                $name = ucfirst($name);
            }

            // create option setter
            $method = 'set' . implode('', $optionName);

            // if setter is defined then call it with the value
            if (in_array($method, $methods)) {
                $this->$method($value);
            }
        }

        return $this;
    }

    /**
     *
     * Convert object to array.
     * @return array
     */
    public function toArray()
    {
        // get properties
        $properties = get_object_vars($this);

        // initialize the array
        $data = array();

        // generate array elements
        foreach ($properties as $property => $value) {

            $propertyName = str_replace('_', '', $property);

            // explode by uppercase
            preg_match_all('/[a-zA-Z][^A-Z]*/', $propertyName, $propertySplitted);

            // lowercase all letters
            foreach ($propertySplitted[0] as &$name)
            {
                $name = lcfirst($name);
            }

            // create property name
            $propertyName = implode('_', $propertySplitted[0]);

            // add property to array
            $data[$propertyName] = $value;
        }

        return $data;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setOrder($order)
    {
        $this->order = $order;
        return $this;
    }

    public function getOrder()
    {
        return $this->order;
    }

    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    public function getText()
    {
        return $this->text;
    }

    public function setPostId($postId)
    {
        $this->postId = $postId;
        return $this;
    }

    public function getPostId()
    {
        return $this->postId;
    }

    public function setColumn($column)
    {
        $this->column = $column;
        return $this;
    }

    public function getColumn()
    {
        return $this->column;
    }
}