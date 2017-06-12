<?php

namespace Krak\Marshal;

describe('#fromXML', function() {
    it('unmarshals simple values', function() {
        $um = fromXML();
        assert($um('<?xml version="1.0"?><root>Value</root>') === 'Value');
    });
    it('unmarshals attributes and text values', function() {
        $um = fromXML();
        assert($um('<?xml version="1.0"?><root a="1">value</root>') === [
            '@a' => '1',
            '#' => 'value'
        ]);
    });
    it('unmarshals child elements and handles extra whitespace', function() {
        $um = fromXML();
        assert($um('<?xml version="1.0"?><root>  <a>1</a>  <b c="3">2</b><d e="4"/>  </root>') === [
            'a' => "1",
            'b' => [
                '@c' => '3',
                '#' => '2'
            ],
            'd' => [
                '@e' => '4',
            ]
        ]);
    });
    it('unmarshals xml arrays', function() {
        $um = fromXML();
        assert($um('<?xml version="1.0"?><root><item>1</item><item>2</item><item a="4">3</item></root>') === [
            'item' => [
                '1',
                '2',
                ['@a' => '4', '#' => '3']
            ]
        ]);
    });
    it('unmarshals xml cdata and handles extra whitespace', function() {
        $um = fromXML();
        assert($um('<?xml version="1.0"?><root>  <![CDATA[<cdata>]]>  </root>') === '<cdata>');
    });
});
