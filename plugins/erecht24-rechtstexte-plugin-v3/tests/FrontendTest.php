<?php

class FrontendTest extends WP_UnitTestCase
{

    /**
     * Test that an erecht24 category gets registered in gutenberg categories
     * and there are no deprecation warnings
     * @return void
     */
    public function test_gutenberg_category_registration()
    {
        $categories = get_block_categories(
            $this->factory()->post->get_object_by_id(1)
        );

        $expected = array(
            'slug' => 'erecht24-blocks',
            'title' => 'eRecht24',
        );

        $this->assertStringContainsStringIgnoringCase(json_encode($expected), json_encode($categories));
    }
}
