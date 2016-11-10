<?php

namespace Charcoal\Attachment\Interfaces;

/**
 * Defines a object that can have attachments.
 */
interface AttachmentContainerInterface
{
    /**
     * The default grouping for contained attachments.
     *
     * @var string
     */
    const DEFAULT_GROUPING = 'generic';

    /**
     * Retrieve the attachments configuration from this object's metadata.
     *
     * @return array
     */
    public function attachmentConfig();

    /**
     * Returns attachable objects
     *
     * @return array Attachable Objects
     */
    public function attachableObjects();

    /**
     * Determine if this attachment is a container.
     *
     * @return boolean
     */
    public function isAttachmentContainer();
}
