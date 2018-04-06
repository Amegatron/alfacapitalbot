<?php
namespace Tests\Unit\Commands;

use App\Core\Parsers\HtmlCourseParser;
use App\Opif;
use App\OpifCourse;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use phpmock\phpunit\PHPMock;
use Tests\TestCase;

class ParseCoursesTest extends TestCase
{
    use DatabaseTransactions;
    use PHPMock;

    /** @test */
    public function a_command_should_fail_parsing()
    {
        $parserMock = \Mockery::mock(HtmlCourseParser::class);
        $parserMock->shouldReceive('parse')
            ->andReturnNull();

        $this->app->bind(HtmlCourseParser::class, function($app) use ($parserMock) {
            return $parserMock;
        });

        $opif = new Opif();
        $opif->name = 'Стратегические инвестиции';
        $opif->fullName = 'Альфа-Капитал Стратегические инвестиции';
        $opif->publicDataUrl = 'https://www.alfacapital.ru/disclosure/pifs/opifsi_fpr/';
        $opif->save();

        $dateMock = $this->getFunctionMock('App\Console\Commands', 'date');
        $dateMock->expects($this->once())->willReturn(20);

        Artisan::call('courses:parse');

        $courses = OpifCourse::where('opif_id', '=', $opif->id)->orderBy('date', 'desc')->get();
        $this->assertCount(0, $courses);
    }

    /** @test */
    public function a_command_should_fill_db_with_courses()
    {
        $prevDate = '05.04.2018';
        $prevCourseRaw = '2 500.00';
        $currDate = '06.04.2018';
        $currCourseRaw = '2 600.00';

        $parserMock = \Mockery::mock(HtmlCourseParser::class);
        $parserMock->shouldReceive('parse')
            ->andReturn([
                'prev' => [
                    'date' => $prevDate,
                    'course' => $prevCourseRaw,
                ],
                'curr' => [
                    'date' => $currDate,
                    'course' => $currCourseRaw,
                ],
            ]);

        $this->app->bind(HtmlCourseParser::class, function($app) use ($parserMock) {
            return $parserMock;
        });

        $opif = new Opif();
        $opif->name = 'Стратегические инвестиции';
        $opif->fullName = 'Альфа-Капитал Стратегические инвестиции';
        $opif->publicDataUrl = 'https://www.alfacapital.ru/disclosure/pifs/opifsi_fpr/';
        $opif->save();

        $dateMock = $this->getFunctionMock('App\Console\Commands', 'date');
        $dateMock->expects($this->once())->willReturn(20);

        Artisan::call('courses:parse');

        $courses = OpifCourse::where('opif_id', '=', $opif->id)->orderBy('date', 'desc')->get();
        $this->assertCount(2, $courses);
        $course = $courses[0];
        $this->assertEquals((new \DateTime($currDate))->format("Y-m-d"), $course->date);
        $this->assertEquals($this->prepareCourse($currCourseRaw), $course->course);
        $course = $courses[1];
        $this->assertEquals((new \DateTime($prevDate))->format("Y-m-d"), $course->date);
        $this->assertEquals($this->prepareCourse($prevCourseRaw), $course->course);
    }

    /** @test */
    public function a_command_should_not_fill_db_with_courses_for_existing_records()
    {
        $prevDate = '05.04.2018';
        $prevCourseRaw = '2 500.00';
        $prevCourseRawNew = '3 500.00';
        $currDate = '06.04.2018';
        $currCourseRaw = '2 600.00';
        $currCourseRawNew = '3 600.00';

        $parserMock = \Mockery::mock(HtmlCourseParser::class);
        $parserMock->shouldReceive('parse')
            ->andReturn([
                'prev' => [
                    'date' => $prevDate,
                    'course' => $prevCourseRawNew,
                ],
                'curr' => [
                    'date' => $currDate,
                    'course' => $currCourseRawNew,
                ],
            ]);

        $this->app->bind(HtmlCourseParser::class, function($app) use ($parserMock) {
            return $parserMock;
        });

        $opif = new Opif();
        $opif->name = 'Стратегические инвестиции';
        $opif->fullName = 'Альфа-Капитал Стратегические инвестиции';
        $opif->publicDataUrl = 'https://www.alfacapital.ru/disclosure/pifs/opifsi_fpr/';
        $opif->save();

        $id = $opif->id;

        OpifCourse::create([
            'date' => new \DateTime($prevDate),
            'opif_id' => $id,
            'course' => $this->prepareCourse($prevCourseRaw),
        ]);
        OpifCourse::create([
            'date' => new \DateTime($currDate),
            'opif_id' => $id,
            'course' => $this->prepareCourse($currCourseRaw),
        ]);

        $dateMock = $this->getFunctionMock('App\Console\Commands', 'date');
        $dateMock->expects($this->once())->willReturn(20);

        Artisan::call('courses:parse');

        $courses = OpifCourse::where('opif_id', '=', $opif->id)->orderBy('date', 'desc')->get();
        $this->assertCount(2, $courses);
        $course = $courses[0];
        $this->assertEquals((new \DateTime($currDate))->format("Y-m-d"), $course->date);
        $this->assertEquals($this->prepareCourse($currCourseRaw), $course->course);
        $course = $courses[1];
        $this->assertEquals((new \DateTime($prevDate))->format("Y-m-d"), $course->date);
        $this->assertEquals($this->prepareCourse($prevCourseRaw), $course->course);
    }

    /** @test */
    public function a_command_should_not_perform_during_idle_hours()
    {
        $dateMock = $this->getFunctionMock('App\Console\Commands', 'date');
        $dateMock->expects($this->once())->willReturn(5);

        Artisan::call('courses:parse');

        $output = trim(Artisan::output());
        $this->assertEquals("Idle hour", $output);
    }

    protected function prepareCourse($course) {
        return (double)preg_replace('~[^0-9.]~', '', $course);
    }
}