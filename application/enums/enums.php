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