<?php

namespace App\Console\Commands;

use App\Core\Parsers\HtmlCourseParser;
use App\Opif;
use App\OpifCourse;
use Illuminate\Console\Command;

class ParseCourses extends Command
{
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

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Exception
     */
    public function handle()
    {
        $opifs = Opif::where('my_amount', '>', 0)->get();
        $parser = new HtmlCourseParser();

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
    }

    protected function prepareCourse($course) {
        return (double)preg_replace('~[^0-9.]~', '', $course);
    }
}
