<?php

namespace App\Console\Commands;

use App\Core\Parsers\HtmlCourseParser;
use App\Opif;
use App\OpifCourse;
use Illuminate\Cache\Repository;
use Illuminate\Console\Command;

class ParseCourses extends Command
{
    const LAST_PARSE_TIME = 'last_parse_time';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'courses:parse';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Parses OPIFs courses';

    protected $cache;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Repository $cache)
    {
        parent::__construct();
        $this->cache = $cache;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Exception
     */
    public function handle()
    {
        $hour = (int)date('H');
        if ($hour >= 0 && $hour <= 10) {
            $this->info("Idle hour");
            return;
        }

        $opifs = Opif::all();
        $parser = app()->make(HtmlCourseParser::class);

        foreach ($opifs as $opif) {
            $this->info($opif->fullName);

            $result = $parser->parse($opif);
            if (is_null($result)) {
                continue;
            }

            $this->info($result['prev']['date'] . " ::" . $result['prev']['course']);
            $this->info($result['curr']['date'] . " ::" . $result['curr']['course']);
            $this->info('');

            $prevDate = new \DateTime($result['prev']['date']);

            $prevDateCourse = OpifCourse::where('date', '=', $prevDate)
                ->where('opif_id', '=', $opif->id)
                ->first();
            if (is_null($prevDateCourse)) {
                $course = OpifCourse::create([
                    'date' => $prevDate,
                    'course' => $this->prepareCourse($result['prev']['course']),
                    'opif_id' => $opif->id,
                ]);
            }

            $currDate = new \DateTime($result['curr']['date']);
            $currDateCourse = OpifCourse::where('date', '=', $currDate)
                ->where('opif_id', '=', $opif->id)
                ->first();
            if (is_null($currDateCourse)) {
                $course = OpifCourse::create([
                    'date' => $currDate,
                    'course' => $this->prepareCourse($result['curr']['course']),
                    'opif_id' => $opif->id,
                ]);
            }
        }

        $this->cache->forever(self::LAST_PARSE_TIME, time());
    }

    protected function prepareCourse($course)
    {
        return (double)preg_replace('~[^0-9.]~', '', $course);
    }
}
