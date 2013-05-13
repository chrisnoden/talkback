<?php
/**
 * Created by Chris Noden using JetBrains PhpStorm.
 * 
 * @author Chris Noden, @chrisnoden
 * @copyright (c) 2009 to 2013 Chris Noden
 */

namespace Talkback\Channel;

interface ChannelInterface
{

    /**
     * Sends the message to the chosen Communication channel
     * @param $msg
     * @return void
     */
    public function write($msg);

    /**
     * Enable output to the channel (default)
     *
     * @return ChannelObject
     */
    public function enable();

    /**
     * Disable output to the channel
     * NB - the channel can still choose to ignore this
     *
     * @return ChannelObject
     */
    public function disable();

    /**
     * @param $delimiter
     * @return ChannelObject
     */
    public function setFieldDelimiter($delimiter);

    /**
     * The field matching the fieldName will show the fixed displayName before the value
     *
     * @param $fieldName
     * @param $displayName
     * @return ChannelObject
     */
    public function showFieldName($fieldName, $displayName);

    /**
     * Add a field/context to our message
     *
     * @param $name
     * @return ChannelObject
     * @throws \Exception
     */
    public function addField($name);

    /**
     * Replaces any defined fields/contexts with the values in the array
     *
     * @param array $aContexts
     */
    public function setFieldValues(array $aContexts);

}