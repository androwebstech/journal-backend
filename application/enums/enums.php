<?php
abstract class USER_TYPE {
    const AUTHOR = 'author';
    const PUBLISHER = 'publisher';
    const REVIEWER = 'reviewer';
    const ALL = [USER_TYPE::AUTHOR,USER_TYPE::PUBLISHER,USER_TYPE::REVIEWER];
}


abstract class APPROVAL_STATUS {
    const PENDING = 'pending';
    const APPROVED = 'approved';
    const REJECTED = 'rejected';
    const ALL = [APPROVAL_STATUS::PENDING,APPROVAL_STATUS::APPROVED,APPROVAL_STATUS::REJECTED];
}


abstract class PR_STATUS {
    const PENDING = 'pending';
    const ACCEPT = 'accept';
    const PROCEED_PAYMENT = 'proceed_payment';
    const PUBLISHED = 'published';
    const REJECT = 'reject';
    const ALL = [PR_STATUS::PENDING,PR_STATUS::ACCEPT,PR_STATUS::PROCEED_PAYMENT,PR_STATUS::PUBLISHED,PR_STATUS::REJECT];
}

abstract class PAYMENT_STATUS {
    const NONE = 'none';
    const PENDING = 'pending';
    const COMPLETE = 'complete';
    const FAILED = 'failed';
    const ALL = [PAYMENT_STATUS::NONE,PAYMENT_STATUS::PENDING,PAYMENT_STATUS::COMPLETE,PAYMENT_STATUS::FAILED];
}