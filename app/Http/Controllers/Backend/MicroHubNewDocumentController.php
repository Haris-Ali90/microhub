<?php

namespace App\Http\Controllers\Backend;

use App\Models\Zones;
use App\Models\JCUser;
use App\DocumentType;
use App\JCDocument;
use App\Models\ZoneSchedule;
use Illuminate\Http\Request;
use App\Models\PreferWorkTime;
use App\Models\PreferWorkType;
use App\Models\MicroHubRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Str;

class MicroHubNewDocumentController  extends BackendController
{

    public function document()
    {
        $documentTypes = DocumentType::where('user_type','micro_hub')->whereNull('deleted_at')->get();
        $documents = JCDocument::where('jc_users_id', '=', auth()->user()->id)->pluck('document_type_id')->toArray();

        return backend_view('new-profile.newdocument', compact('documents','documentTypes'));

    }

    public function getTrainingNewDocuments()
    {
        $documentTypes = DocumentType::where('user_type','micro_hub')->whereNull('deleted_at')->get();
        $documents = JCDocument::where('jc_users_id', '=', auth()->user()->id)->pluck('document_type_id')->toArray();
        return backend_view('new-profile.newdocument', compact('documents','documentTypes'));
  }

    public function addDocument(Request $request)
    {
        $input = $request->all();
        $data = $request->except(
            [
                '_token',
                '_method',
            ]
        );
        $joey = auth()->user();

        if (!empty($input['documentIds'])) {
            foreach ($input['documentIds'] as $doc_id) {
                $doc_type = DocumentType::where('id', $doc_id)->first();
                if ($doc_type->document_type == 'file') {
                    if (!empty($input['document']) && array_key_exists($doc_type->id,$input['document'])) {

                        if(gettype($input['document']) == 'object')
                        {
                            $slug = Str::slug($joey->full_name, '-');
                            $file = $input['document'][$doc_type->id];
                            $extension = $file->getClientOriginalExtension(); // getting image extension
                            $filename = $slug . '-' . rand() . '-' . time() . '.' . $extension;
                            $file->move('microhub-user/document/', $filename);
                            $imageUrl = url('microhub-user/document/' . $filename);

                            $exp = isset($input['expireDate'][$doc_type->id]) ? $input['expireDate'][$doc_type->id] : null;
                            JCDocument::where('document_type_id', '=', $doc_type->id)->where('jc_users_id', '=', $joey->id)->update(['deleted_at' => date("Y-m-d h:i:s")]);
                            JCDocument::create(['document_type_id' => $doc_type->id, 'jc_users_id' => $joey->id, 'document_data' => $imageUrl, 'exp_date' => $exp, 'document_type' => $doc_type->document_name]);

                        }
                    }
                }
                if ($doc_type->document_type == 'sin') {

                    JCDocument::where('document_type_id', '=', $doc_type->id)->where('jc_users_id', '=', $joey->id)->update(['deleted_at' => date("Y-m-d h:i:s")]);
                    $exp = isset($input['expireDate'][$doc_type->id])?$input['expireDate'][$doc_type->id]:null;
                    if ($input['sin'] == 'temporary'){
                        JCDocument::create(['document_type_id' => $doc_type->id,'jc_users_id' => $joey->id, 'document_data' => $input['documenttext'][$doc_type->id], 'exp_date' => $exp, 'document_type' => $doc_type->document_name]);
                    }
                    else{

                        JCDocument::create(['document_type_id' => $doc_type->id,'jc_users_id' => $joey->id, 'document_data' => $input['documenttext'][$doc_type->id], 'exp_date' => null, 'document_type' => $doc_type->document_name]);
                    }

                }
                if ($doc_type->document_type == 'text') {
                    $exp = isset($input['expireDate'][$doc_type->id])?$input['expireDate'][$doc_type->id]:null;
                    JCDocument::where('document_type_id', '=', $doc_type->id)->where('jc_users_id', '=', $joey->id)->update(['deleted_at' => date("Y-m-d h:i:s")]);
                    JCDocument::create(['document_type_id' => $doc_type->id,'jc_users_id' => $joey->id, 'document_data' => $input['documenttext'][$doc_type->id], 'exp_date' => $exp, 'document_type' => $doc_type->document_name]);
                }
            }
        }

        return redirect('microhub/newtraining');

    }



}