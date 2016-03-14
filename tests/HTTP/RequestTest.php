<?php 

namespace ImageButler\Test\HTTP;

use ImageButler\HTTP\Request;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test Request::fromGlobals
     */
    public function testFromGlobals()
    {
        $request = Request::fromGlobals();
        $this->assertInstanceOf('ImageButler\\HTTP\\Request', $request);
    }

    /**
     * Test Request::method
     */
    public function testMethod()
    {
    	$request = new Request('GET');
        $this->assertEquals('GET', $request->method());

        // test lowercase
        $request = new Request('post');
        $this->assertEquals('POST', $request->method());

        // test invalid
        $request = new Request('WtF');
        $this->assertEquals('GET', $request->method());
    }

    /**
     * Test Request::uri
     */
    public function testUri()
    {
        $request = new Request('GET', '/hello');
        $this->assertEquals('/hello', $request->uri());

        // missing starting slash
        $request = new Request('GET', 'hello');
        $this->assertEquals('/hello', $request->uri());

        // cutting of the query parameters
        $request = new Request('GET', 'hello?nope=this&is=the&wrong=place');
        $this->assertEquals('/hello', $request->uri());

        // string convert
        $request = new Request('GET', 123);
        $this->assertEquals('/123', $request->uri());
    }

    /**
     * Test Request::get
     */
    public function testGet()
    {
        $request = new Request('GET', '/', array('name' => 'John'));
        $this->assertEquals('John', $request->get('name'));

        // test default
        $request = new Request('GET', '/', array('name' => 'John'));
        $this->assertEquals('Oliver', $request->get('lastname', 'Oliver'));
    }

    /**
     * Test Request::post
     */
    public function testPost()
    {
        $request = new Request('POST', '/', array(), array('name' => 'John'));
        $this->assertEquals('John', $request->post('name'));

        // test default
        $request = new Request('POST', '/', array(), array('name' => 'John'));
        $this->assertEquals('Oliver', $request->post('lastname', 'Oliver'));
    }

    /**
     * Test Request::cookie
     */
    public function testCookie()
    {
        $request = new Request('GET', '/', array(), array(), array('name' => 'John'));
        $this->assertEquals('John', $request->cookie('name'));

        // test default
        $request = new Request('GET', '/', array(), array(), array('name' => 'John'));
        $this->assertEquals('Oliver', $request->cookie('lastname', 'Oliver'));
    }

    /**
     * Test Request::file
     */
    public function testFile()
    {
        $request = new Request('GET', '/', array(), array(), array(), array('name' => 'John'));
        $this->assertEquals('John', $request->file('name'));

        // test default
        $request = new Request('GET', '/', array(), array(), array(), array('name' => 'John'));
        $this->assertEquals('Oliver', $request->file('lastname', 'Oliver'));
    }

    /**
     * Test Request::server
     */
    public function testServer()
    {
        $request = new Request('GET', '/', array(), array(), array(), array(), array('name' => 'John'));
        $this->assertEquals('John', $request->server('name'));

        // test default
        $request = new Request('GET', '/', array(), array(), array(), array(), array('name' => 'John'));
        $this->assertEquals('Oliver', $request->server('lastname', 'Oliver'));
    }
}
