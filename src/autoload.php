<?php
// @codingStandardsIgnoreFile
// @codeCoverageIgnoreStart
// this is an autogenerated file - do not edit
spl_autoload_register(
    function($class) {
        static $classes = null;
        if ($classes === null) {
            $classes = array(
                'greencape\\allpairs\\combinator' => '/GreenCape/Combinator.php',
                'greencape\\allpairs\\consolewriter' => '/GreenCape/AllPairs/Writer/ConsoleWriter.php',
                'greencape\\allpairs\\defaultstrategy' => '/GreenCape/AllPairs/Strategy/DefaultStrategy.php',
                'greencape\\allpairs\\filereader' => '/GreenCape/AllPairs/Reader/FileReader.php',
                'greencape\\allpairs\\pairhash' => '/GreenCape/AllPairs/PairHash.php',
                'greencape\\allpairs\\parameter' => '/GreenCape/AllPairs/Parameter.php',
                'greencape\\allpairs\\qictstrategy' => '/GreenCape/AllPairs/Strategy/QictStrategy.php',
                'greencape\\allpairs\\reader' => '/GreenCape/AllPairs/Reader.php',
                'greencape\\allpairs\\strategy' => '/GreenCape/AllPairs/Strategy.php',
                'greencape\\allpairs\\stringreader' => '/GreenCape/AllPairs/Reader/StringReader.php',
                'greencape\\allpairs\\vardumpwriter' => '/GreenCape/AllPairs/Writer/VardumpWriter.php',
                'greencape\\allpairs\\writer' => '/GreenCape/AllPairs/Writer.php'
            );
        }
        $cn = strtolower($class);
        if (isset($classes[$cn])) {
            require __DIR__ . $classes[$cn];
        }
    }
);
// @codeCoverageIgnoreEnd
