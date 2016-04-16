<?php
protected function setPageBodyContent($result, $isJson = false){
        $result = ($isJson == true) ? Mage::helper('core')->jsonEncode($result):$result;
        $contentType = ($isJson == true) ? 'json':'html';
        
        $this->getResponse()
                ->setHttpResponseCode( 200 )
                ->setHeader( 'Pragma', 'public', true )
                ->setHeader( 'Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true )
                ->setHeader( 'Content-type', 'text/'.$contentType, true )
                ->setHeader( 'Content-Length', strlen( $result ) )
                ->setHeader( 'Last-Modified', date( 'r' ) )
                ->setBody( $result );
    }
?>