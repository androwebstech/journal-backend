<?php
abstract class USER_TYPE {
    const AUTHOR = 'author';
    const PUBLISHER = 'publisher';
    const REVIEWER = 'reviewer';
    const ALL = [USER_TYPE::AUTHOR,USER_TYPE::PUBLISHER,USER_TYPE::REVIEWER];
}