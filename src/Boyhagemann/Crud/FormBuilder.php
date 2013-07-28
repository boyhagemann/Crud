<?php

namespace Boyhagemann\Crud;

use Symfony\Component\Form\FormBuilder as FormFactory;
use Boyhagemann\Crud\FormBuilder\InputElement;
use Boyhagemann\Crud\FormBuilder\CheckableElement;
use Boyhagemann\Crud\FormBuilder\ModelElement;
use Event;

class FormBuilder
{
    /**
     * @var FormFactory
     */
    protected $factory;

    /**
     * @param FormFactory $factory
     */
    public function __construct(FormFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * 
     * @param array $values
     * @return $this
     */
    public function defaults(Array $values = array())
    {
        foreach ($this->elements as $name => $element) {
            if (isset($values[$name])) {
                $element->value($values[$name]);
            }
        }

        return $this;
    }

    /**
     * @return \Symfony\Component\Form\Form
     */
    public function build()
    {
        $reference = $this;
        $factory = $this->factory;
        
        foreach ($this->elements as $name => $element) {
            
            Event::fire('formBuilder.buildElement.pre', compact('name', 'element', 'factory', 'reference'));

            $this->factory->add($name, $element->getType(), $element->getOptions());

            Event::fire('formBuilder.buildElement.post', compact('name', 'element', 'factory', 'reference'));
            
        }

        return $this->getFactory()->getForm();
    }

    /**
     * @return mixed
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * @param       $name
     * @param       $element
     * @param       $type
     * @param array $options
     * @return InputElement|CheckableElement|ModelElement
     */
    protected function addElement($name, $element, $type, Array $options = array())
    {
        $reference = $this;
        Event::fire('formBuilder.addElement.pre', compact('name', 'element', 'type', 'options', 'reference'));
        
        switch ($element) {

            case 'input':
                $element = new InputElement($name, $type, $options);
                break;

            case 'checkable':
                $element = new CheckableElement($name, $type, $options);
                break;

            case 'model':
                $element = new ModelElement($name, $type, $options);
                break;
        }

        $this->elements[$name] = $element;
        
        Event::fire('formBuilder.addElement.post', compact('name', 'element', 'type', 'options', 'reference'));
        
        return $element;
    }

    /**
     * @param string $name
     * @return InputElement
     */
    public function text($name)
    {
        return $this->addElement($name, 'input', 'text');
    }

    /**
     * @param $name
     * @return InputElement
     */
    public function textarea($name)
    {
        return $this->addElement($name, 'input', 'textarea');
    }

    /**
     * @param $name
     * @return InputElement
     */
    public function integer($name)
    {
        return $this->addElement($name, 'input', 'integer');
    }

    /**
     * @param $name
     * @return InputElement
     */
    public function percentage($name)
    {
        return $this->addElement($name, 'input', 'percent');
    }

    /**
     * @param $name
     * @return CheckableElement
     */
    public function select($name)
    {
        return $this->addElement($name, 'checkable', 'choice', array(
                    'multiple' => false,
                    'expanded' => false,
        ));
    }

    /**
     * @param $name
     * @return CheckableElement
     */
    public function multiselect($name)
    {
        return $this->addElement($name, 'checkable', 'choice', array(
                    'multiple' => true,
                    'expanded' => false,
        ));
    }

    /**
     * @param $name
     * @return CheckableElement
     */
    public function radio($name)
    {
        return $this->addElement($name, 'checkable', 'choice', array(
                    'multiple' => false,
                    'expanded' => true,
        ));
    }

    /**
     * @param $name
     * @return CheckableElement
     */
    public function checkbox($name)
    {
        return $this->addElement($name, 'checkable', 'choice', array(
                    'multiple' => true,
                    'expanded' => true,
        ));
    }

    /**
     * @param $name
     * @return InputElement
     */
    public function submit($name)
    {
        return $this->addElement($name, 'input', 'submit');
    }

    /**
     * @param $name
     * @return ModelElement
     */
    public function modelSelect($name)
    {
        return $this->addElement($name, 'model', 'choice');
    }

    /**
     * @param $name
     * @return ModelElement
     */
    public function modelRadio($name)
    {
        return $this->addElement($name, 'model', 'choice', array(
                    'multiple' => true,
                    'expanded' => true,
        ));
    }

    /**
     * @param $name
     * @return ModelElement
     */
    public function modelCheckbox($name)
    {
        return $this->addElement($name, 'model', 'choice', array(
                    'multiple' => true,
                    'expanded' => false,
        ));
    }

}