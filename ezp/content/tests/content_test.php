<?php
/**
 * File contains: ezp\content\tests\ContentTest class
 *
 * @copyright Copyright (C) 1999-2011 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 * @package ezp
 * @subpackage content_tests
 */

namespace ezp\content\tests;

/**
 * Test case for Content class
 *
 * @package ezp
 * @subpackage content_tests
 */
use \ezp\content\Content, \ezp\content\Location, \ezp\content\Translation;
class ContentTest extends \PHPUnit_Framework_TestCase
{
    protected $contentType;

    protected $localeFR;
    protected $localeEN;

    public function __construct()
    {
        parent::__construct();
        $this->setName( "Content class tests" );

        // setup a content type & content object of use by tests
        $this->contentType = new \ezp\content\type\Type();
        $this->contentType->identifier = 'article';

        // Add some fields
        $fields = array( 'title' => 'ezstring', 'tags' => 'ezkeyword' );
        foreach ( $fields as $identifier => $fieldTypeString )
        {
            $field = new \ezp\content\type\Field( $this->contentType );
            $field->identifier = $identifier;
            $field->fieldTypeString = $fieldTypeString;
            $this->contentType->fields[] = $field;
        }

        $this->localeFR = new \ezp\base\Locale( 'fre-FR' );
        $this->localeEN = new \ezp\base\Locale( 'eng-GB' );
    }

    /**
     * Test the default Translation internally created with a Content is created
     */
    public function testDefaultContentTranslation()
    {
        $content = new Content( $this->contentType, $this->localeEN );
        $tr = $content->translations['eng-GB'];
        self::assertEquals( count( $content->translations ), 1 );
        self::assertEquals( count( $content->versions ), 1 );
        self::assertEquals( count( $tr->versions ), 1 );
        self::assertEquals( $tr->locale->code, $this->localeEN->code );
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testContentAddExistingTranslation()
    {
        $content = new Content( $this->contentType, $this->localeEN );
        $content->addTranslation( $this->localeEN );
    }

    /**
     * Test Content::addTranslation() behaviour:
     * - new Translation has the right locale
     * - new Translation has one version
     * - a new version is also added to the Content
     */
    public function testContentAddTranslation()
    {
        $content = new Content( $this->contentType, $this->localeEN );
        $tr = $content->addTranslation( $this->localeFR );
        self::assertEquals( $tr->locale->code, $this->localeFR->code );
        self::assertEquals( count( $tr->versions ), 1 );
        self::assertEquals( count( $content->versions ), 2 );
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testLocationWrongClass()
    {
        $content = new Content( $this->contentType, new \ezp\base\Locale( 'eng-GB' ) );
        $content->locations[] = \ezp\content\Section::__set_state( array( 'id' => 1 ) );
    }

    /**
     * Test that foreign side of relation is updated for Location -> Content when Location is created
     */
    public function testContentLocationWhenLocationIsCreated()
    {
        $content = new Content( $this->contentType, new \ezp\base\Locale( 'eng-GB' ) );
        $location = new Location( $content );
        $this->assertEquals( $location, $content->locations[0], 'Location on Content is not correctly updated when Location is created with content in constructor!' );
        $content->locations[] = $location;
        $this->assertEquals( 1, count( $content->locations ), 'Collection allows several instances of same object!' );
    }
}
