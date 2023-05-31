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
    $about = About::wherestatus('published')->select('salutation', 'name', 'featured_image', 'slogan')->first();
    $companies = Company::wherestatus(1)->orderby('start_date', 'desc')->limit(5)->get();
    $contacts = Contact::wherestatus(1)->orderby('importance', 'desc')->limit(3)->get();
    $skills_categories = SkillsCategory::with('skills')->wherestatus(1)->orderby('importance', 'desc')->limit(3)->get();;
    $projects = Project::with(['company', 'skills'])->wherestatus('published')->orderby('importance', 'desc')->limit(3)->get();
    $projects = $this->select($projects);
    $qualifications = Qualification::wherestatus(1)->orderby('importance', 'desc')->limit(3)->get();
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
    return $pdf->download($this->data()['about']->name . ' resume.pdf');
  }

  private function select($q)
    {
        return $q->map(
            function ($q) {
                return [
                    ...$q->only([
                        '_id',
                        'title',
                        'slug',
                        'content_short',
                        'featured_image',
                        'project_url'
                    ]),
                    'company' => $q->company()->first(['name']),
                    'skills' => $q->skills()->get(['name'])
                ];
            }
        );
    }
}
