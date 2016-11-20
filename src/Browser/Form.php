<?php
/**
 * Created by PhpStorm.
 * User: shellus
 * Date: 2016-11-20
 * Time: 18:49
 */

namespace Browser;

use Symfony\Component\DomCrawler\Field\InputFormField;

class Form extends \Symfony\Component\DomCrawler\Form
{
    /** @var Browser $browser */
    protected $browser = null;

    public function __construct(\DOMElement $node, $currentUri, $method, $baseHref, $browser)
    {
        parent::__construct($node, $currentUri, $method, $baseHref);

        $this->browser = $browser;
    }

    public function setText($name, $value)
    {
        $this->get($name)->setValue($value);
    }

    public function addText($name, $value)
    {
        $el = new \DOMElement('input');
        $dom = new \DOMDocument();
        $dom->appendChild($el);
        $el->setAttribute('name', $name);
        $el->setAttribute('value', $value);
        $this->set(new InputFormField($el));
    }

    public function submit()
    {
        $fields = $this->all();

        $inputs = [];
        foreach ($fields as $field) {
            $inputs[$field->getName()] = $field->getValue();
        }
        
        $url = $this->currentUri;

        $body = $this->browser->request($url, $this->getMethod(), $inputs);

        $this->browser->change($body, $url);

        return true;
    }
}