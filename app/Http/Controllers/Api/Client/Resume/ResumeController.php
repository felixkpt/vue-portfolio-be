<?php

namespace App\Http\Controllers\Api\Client\Resume;

use App\Http\Controllers\Controller;
use App\Models\About;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Project;
use App\Models\Qualification;
use App\Models\SkillsCategory;
use PDF;

class ResumeController extends Controller
{
  // Display resume
  public function index()
  {
    return view('resume/index', $this->data());
  }

  private function data()
  {
    $about = About::wherestatus('published')->first();
    $companies = Company::wherestatus(1)->limit(5)->get();
    $contacts = Contact::wherestatus(1)->limit(5)->get();
    $skills_categories = SkillsCategory::wherestatus(1)->with('skills')->limit(5)->get();;
    $projects = Project::wherestatus('published')->with(['company', 'skills'])->limit(3)->get();
    $qualifications = Qualification::wherestatus(1)->limit(5)->get();
    return [
      'about' => $about,
      'companies' => $companies,
      'contacts' => $contacts,
      'skills_categories' => $skills_categories,
      'projects' => $projects,
      'qualifications' => $qualifications,
    ];
  }
  // Generate PDF
  public function download()
  {
    // share data to view
    view()->share($this->data());

    $pdf = PDF::loadView('resume/pdf_view', [])->setOption([]);
    // download PDF file with download method
    return $pdf->download($this->data()['about']->name.' resume.pdf');
  }
}
