<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\CreditLimit\Model\Transaction\Builder;

use Aheadworks\CreditLimit\Api\Data\TransactionInterface;
use Aheadworks\CreditLimit\Api\Data\TransactionParametersInterface;
use Aheadworks\CreditLimit\Model\Transaction\TransactionBuilderInterface;

/**
 * Class CommentToCustomer
 *
 * @package Aheadworks\CreditLimit\Model\Transaction\Builder
 */
class CommentToAdmin extends AbstractBuilder implements TransactionBuilderInterface
{
    /**
     * @inheritdoc
     */
    public function checkIsValid(TransactionParametersInterface $params)
    {
        return $params->getCommentToAdmin() !== null;
    }

    /**
     * @inheritdoc
     */
    public function build(TransactionInterface $transaction, TransactionParametersInterface $params)
    {
        $transaction->setCommentToAdmin($params->getCommentToAdmin());
    }
}
