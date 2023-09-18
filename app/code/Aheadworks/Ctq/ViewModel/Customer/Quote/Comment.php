<?php
/**
 * Copyright 2020 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Ctq\ViewModel\Customer\Quote;

use Aheadworks\Ctq\Api\Data\CommentInterface;
use Aheadworks\Ctq\Model\Quote\Url;
use Aheadworks\Ctq\Model\Source\Owner;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\App\Area;

/**
 * Class Comment
 * @package Aheadworks\Ctq\ViewModel\Customer\Quote
 */
class Comment implements ArgumentInterface
{
    /**
     * @var Url
     */
    private $url;

    /**
     * @var TimezoneInterface
     */
    private $localeDate;

    /**
     * @var string
     */
    private $area;

    /**
     * @param Url $url
     * @param TimezoneInterface $localeDate
     * @param string $area
     */
    public function __construct(
        Url $url,
        TimezoneInterface $localeDate,
        $area = Area::AREA_FRONTEND
    ) {
        $this->url = $url;
        $this->localeDate = $localeDate;
        $this->area = $area;
    }

    /**
     * Retrieve comment classes
     *
     * @param CommentInterface $comment
     * @return string
     */
    public function getCommentClasses($comment)
    {
        $classNames = ['comment'];
        if ($comment->getOwnerType() == Owner::SELLER) {
            $classNames[] = 'seller';
        }
        if ($comment->getOwnerType() == Owner::BUYER) {
            $classNames[] = 'buyer';
        }

        return implode(' ', $classNames);
    }

    /**
     * Retrieve formatted created at date
     *
     * @param string $createdAt
     * @return string
     */
    public function getCreatedAtFormatted($createdAt)
    {
        return $this->localeDate->formatDateTime($createdAt, \IntlDateFormatter::MEDIUM, \IntlDateFormatter::MEDIUM);
    }

    /**
     * Retrieve owner name
     *
     * @param CommentInterface $comment
     * @return string
     */
    public function getOwnerName($comment)
    {
        return $comment->getOwnerName();
    }

    /**
     * Retrieve downloadable url
     *
     * @param string $attachmentFileName
     * @param int $quoteId
     * @param int $commentId
     * @param bool|null $isSeller
     * @return string
     */
    public function getDownloadUrl($attachmentFileName, $quoteId, $commentId, $isSeller = null)
    {
        if ($isSeller) {
            $url = $this->url->getAdminDownloadUrl($attachmentFileName, $quoteId, $commentId);
        } elseif ($isSeller === false) {
            $url = $this->url->getFrontendDownloadUrl($attachmentFileName, $quoteId, $commentId);
        } else {
            $url = $this->url->getDownloadUrl($attachmentFileName, $quoteId, $commentId);
        }
        return $url;
    }

    /**
     * Retrieve add comment url
     *
     * @return string
     */
    public function getAddCommentUrl()
    {
        return $this->url->getAddCommentUrl();
    }

    /**
     * Is need to display submit button
     *
     * @return string
     */
    public function isSubmitButtonDisplayed()
    {
        return $this->area == Area::AREA_FRONTEND;
    }
}
