<?php

interface Controller_Interface {

    public function requestDispatched($docId, ContentType $outputContentType = null, $bin = null);

    public function GET();
    public function POST();
    public function DELETE();
}
