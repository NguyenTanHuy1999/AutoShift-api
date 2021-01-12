<?php


namespace App\Http\Controllers\Api\V1;

use App\Api\Entities\Kpi;
use App\Api\Entities\Student;
use App\Api\Entities\Subject;
use App\Api\Repositories\Contracts\StudentRepository;
use App\Http\Controllers\Controller;
use Dompdf\Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Laravel\Lumen\Http\Redirector;
use DB;

class StudentController extends Controller
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var StudentRepository
     */
    private $studentRepository;

    /**
     * StudentController constructor.
     * @param Request           $request
     * @param StudentRepository $studentRepository
     */
    public function __construct(
        Request $request,
        StudentRepository $studentRepository
    )
    {
        $this->request = $request;
        $this->studentRepository = $studentRepository;
    }

    /**
     * @return View
     */
    public function viewList() {
////        $students = $this->studentRepository->paginate();
//        $students = Student::all();
//        $viewData = [
//            'students' => $students
//        ];
//        return view('student-list', $viewData);
        dd(DB::connection()->getPdo());
    }

    /**
     * @return View
     */
    public function viewCreateForm() {

        if ($this->request->isMethod('POST')) {
            $validator = \Validator::make($this->request->all(), [
                'identification_num' => 'numeric|required',
                'full_name' => 'string|required',
                'course_name' => 'string|nullable'
            ]);

            if ($validator->fails()) {
                return redirect('api/students/create-form');
            }

            // Input
            $full_name = $this->request->get('full_name');
            $course_name = $this->request->get('course_name');
            $identification_num = $this->request->get('identification_num');

            try {
//                $student = $this->studentRepository->create([
//                    'full_name' => $full_name,
//                    'identification_num' => $identification_num,
//                    'course_name' => $course_name,
//                ]);
                $student = Student::create([
                    'full_name' => $full_name,
                    'identification_num' => $identification_num,
                    'course_name' => $course_name,
                ]);
            }catch(\Exception $e) {
                return view('add-student-form', [
                    "isDuplicate" => true,
                    'full_name' => $full_name,
                    'identification_num' => $identification_num,
                    'course_name' => $course_name,
                ]);
            }

            return redirect('api/students/view-list');
        }

        return view('add-student-form', [
            "isDuplicate" => false,
            'full_name' => '',
            'identification_num' => '',
            'course_name' => '',
        ]);
    }

    /**
     * @return RedirectResponse|Redirector
     * @throws \Exception
     */
    public function delete() {
        // Input
        $id = $this->request->get('id');

        // Delete
        $student = Student::where('id', $id)->first();
        if (!empty($student)) {
            $student->delete();
        }
        return redirect('api/students/view-list');
    }
}