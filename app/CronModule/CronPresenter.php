<?php

namespace App\CronModule\Presenters;

use App\Model\Stats;
use App\Model\Stat;
use App\Model\SystemLogs;
use App;


class CronPresenter extends App\Presenters\BasePresenter
{
    /** @var string */
    protected $cronName = '';

    /** @var Stats @inject */
    public $stats;

    /** @var SystemLogs @inject */
    public $systemLogs;


    protected function startup()
    {
        parent::startup();
        if($_SERVER['SERVER_ADDR'] != $_SERVER['REMOTE_ADDR']) {
            $this->error('Access denied', 403);
        }
    }

    /**
     * Executed every minute
     */
    public function actionMinute()
    {
        $this->cronName = 'cron_minute';
        $this->stats->createMinuteStats();
    }

    /**
     * Executed every hour
     */
    public function actionHour()
    {
        $this->cronName = 'cron_hour';
    }

    /**
     * Executed at midnight
     * @TODO: timezone?
     */
    public function actionMidnight()
    {
        $this->cronName = 'cron_midnight';
    }

    /**
     * Executed at 04:00
     * CPU Heavy / unimportant tasks should be here
     */
    public function actionNight()
    {
        $this->cronName = 'cron_night';

        // maily (neaktivní hráči)
        // čistění db
        // vyhodnocení affil
    }

    protected function beforeRender()
    {
        parent::beforeRender();
        $this->systemLogs->log($this->cronName, 'finished', 'Cron success');
        exit('Cron success.');
    }
}