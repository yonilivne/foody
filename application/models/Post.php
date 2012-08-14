<?php
/**
 * Created by IntelliJ IDEA.
 * User: vladvidican
 * Date: 7/26/12
 * Time: 4:45 PM
 */
class Model_Post
{
    private $id;
    private $title;
    private $description;
    private $thumb;

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

    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    public function getDescription()
    {
        return $this->description;
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

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setThumb($thumb)
    {
        $this->thumb = $thumb;
        return $this;
    }

    public function getThumb()
    {
        return $this->thumb;
    }
}
