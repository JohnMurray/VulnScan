<?php
function testThrowStatementHasExpectedStartColumn($bar, $object)
{
    throw new \RuntimeException(
        "Foo" . $bar . $object->baz );
}

testThrowStatementHasExpectedEndColumn("23", (object) array("baz" => 42));