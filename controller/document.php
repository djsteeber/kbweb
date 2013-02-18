<?php

  require_once('model/lookup_types.php');
  class DocumentController extends Controller {
   //REFACTOR:  removed EditableController, and write your own
    public function __construct() {
      parent::__construct();
      $this->addSecurityRole('index', 'admin');
    }

    public function index($request = null) {
      $data = getMapValue($request, 'data');
      $action = getMapValue($data, 'document_action', 'view');
      $doc_type = getMapValue($data, 'doc_type', 4);
      $model = array();
      $odb = $this->getODB();
      $docTypes = $odb->fetchAll('DocumentType');
      $model['doc_types'] = $docTypes;
      $model['doc_type'] = $doc_type;

      if ($action == 'save') {
        if ($this->storeUploadedDocument($data)) {
          $docName = getMapValue($data, 'file_name');
        #  $model['message'] = "Upload of $docName Complete";
        } else {
          $model['message'] = 'Unable to upload the document';
        }
      } else {
        $docType = $this->findDocType($docTypes, $doc_type);
        $fn = getMapValue($data, 'document_name');
        $fp = $_SERVER['DOCUMENT_ROOT'] . $docType->rootpath . '/' . $fn;

        if ($action == 'delete') {
          if (is_file($fp)) { 
            unlink($fp);
          }
        } else if ($action == 'archive') {
          $archivefp = $_SERVER['DOCUMENT_ROOT'] 
                  . $docType->rootpath . '/archive/' . $fn;
          if (is_file($fp)) { 
            if (is_file($archivefp)) { 
              unlink($archivefp);
            }
            rename($fp, $archivefp);
          }
        } else if ($action == 'unarchive') {
          $archivefp = $_SERVER['DOCUMENT_ROOT'] 
                  . $docType->rootpath . '/archive/' . $fn;
          if (is_file($archivefp)) { 
            if (is_file($fp)) { 
              unlink($fp);
            }
            rename($archivefp, $fp);
          }
        }
      }

      $model['file_list'] = $this->getDocumentList($docTypes
                                                  ,$doc_type);
      $this->setModel($model);
      $this->setView('view/document.php');
    }

    private function findDocType(&$docTypes, $dtID) {
      $rtn = null;
      if ($dtID == null) {
        if (($docTypes != null) and (count($docTypes) > 0)) {
          $rtn = $docTypes[0];
        }
      } else {
        foreach ($docTypes as $dt) {
          if ($dtID == $dt->id) {
            $rtn = $dt;
          }
        }
      }
      return $rtn;
    }

    private function getDocumentList(&$docTypes, $dtID) {
      $ary = array();
      $docType = $this->findDocType($docTypes, $dtID);
      return $docType->getDocumentList('names', true);
    }

    private function storeUploadedDocument($data) {
      $uploadedFile = getMapValue($data ,'uploadedfile');
      if ($uploadedFile == null) {
        error_log('uploaded file is null');
        return false;
      }

      $fp = getMapValue($uploadedFile, 'tmp_name');
      if ($fp == null) {
        error_log('uploaded file tmp_name is null');
        return false;
      }
      if (! is_uploaded_file($fp)) {
        error_log('is upload file is false');
        return false;
      }

      $target_path = $this->getTargetPath($data);
      $rc = move_uploaded_file($fp, $target_path);
      if (! $rc) {
         error_log('error moving file');
      }

      return $rc;

    }

    private function getTargetPath($data) {
      $dt_id = getMapValue($data, 'doc_type');
      $file_name = getMapValue($data, 'file_name');
      //add in string replace to remove spaces and / \ characters
      $odb = $this->getODB();
      $dt = $odb->fetch('DocumentType', $dt_id);
      return $_SERVER['DOCUMENT_ROOT'] . $dt->rootpath . '/' . $file_name;
    }

  }

?>
