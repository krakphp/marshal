<?php

namespace Krak\Marshal;

use DOMDocument;
use DOMNode;
use DOMElement;
use DOMText;

/** converts XML into an array. */
function fromXML() {
    return function($xml_contents) {
        $doc = new DOMDocument();
        $doc->loadXML($xml_contents);
        $root = $doc->documentElement;

        return _xmlNodeToArray($root);
    };
}

function _xmlNodeToArray(DOMNode $node) {
    $child_nodes = Util\filter($node->childNodes, function($child) {
        return $child instanceof DOMElement;
    });

    if (!$node->attributes->length && !count($child_nodes)) {
        return _xmlNodeTextContent($node);
    }
    $data = Util\reduce($node->attributes, function($acc, $attr) {
        $acc['@'.$attr->name] = $attr->value;
        return $acc;
    }, []);
    if (!count($child_nodes) && $node->nodeValue) {
        $data['#'] = _xmlNodeTextContent($node);
    }

    if (!count($child_nodes)) {
        return $data;
    }

    $is_array = count($child_nodes) > 1 && Util\all($child_nodes, function($child) use ($child_nodes) {
        return $child->nodeName === $child_nodes[0]->nodeName;
    });

    if ($is_array) {
        $data[$child_nodes[0]->nodeName] = Util\map($child_nodes, 'Krak\Marshal\_xmlNodeToArray');
    } else {
        $data = Util\reduce($child_nodes, function($acc, $node) {
            $acc[$node->nodeName] = _xmlNodeToArray($node);
            return $acc;
        }, $data);
    }

    return $data;
}

function _xmlNodeTextContent(DOMNode $node) {
    $text_nodes = Util\filter($node->childNodes, function($child) {
        return $child instanceof DOMText;
    });

    if (count($text_nodes) == 1) {
        return $text_nodes[0]->wholeText;
    }

    return Util\reduce($text_nodes, function($acc, $node) {
        return $node->isWhitespaceInElementContent()
            ? $acc
            : $acc . $node->nodeValue;
    }, '');
}
