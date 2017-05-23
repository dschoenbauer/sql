<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace DSchoenbauer\Sql\Exception;

use DSchoenbauer\Exception\Http\ClientError\NotFoundException;

/**
 * No Records are being impacted by the query
 *
 * @author David Schoenbauer
 */
class NoRecordsAffectedException extends NotFoundException implements SqlExceptionInterface
{
    
}
