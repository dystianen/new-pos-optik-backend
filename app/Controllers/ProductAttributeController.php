<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ProductAttributeModel;

class ProductAttributeController extends BaseController
{
    protected $attributeModel;

    public function __construct()
    {
        $this->attributeModel = new ProductAttributeModel();
    }

    public function apiListProductCategory()
    {
        $attributes = $this->attributeModel->findAll();

        $response = [
            'status' => 200,
            'message' => 'Successfully',
            'data' => $attributes
        ];
        return $this->response->setJSON($response);
    }

    // READ
    public function webIndex()
    {
        $page = $this->request->getVar('page') ?? 1;
        $perPage = 10;
        $attributes = $this->attributeModel->paginate($perPage, 'default', $page);
        $pager = [
            'currentPage' => $this->attributeModel->pager->getCurrentPage('default'),
            'totalPages' => $this->attributeModel->pager->getPageCount('default'),
            'limit' => $perPage
        ];

        return view('product_attribute/v_index', [
            'attributes' => $attributes,
            'pager' => $pager
        ]);
    }

    public function form()
    {

        $id = $this->request->getVar('id');
        $data = [];
        if ($id) {
            $attribute = $this->attributeModel->find($id);
            if (!$attribute) {
                return redirect()->to('/product-attribute')->with('failed', 'Product attribute not found.');
            }
            $data['attribute'] = $attribute;
        }

        return view('product_attribute/v_form', $data);
    }

    public function save()
    {
        $id = $this->request->getVar('id');
        $rules = $this->attributeModel->validationRules;

        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $errorMessage = implode('<br>', $errors);

            return redirect()->back()
                ->withInput()
                ->with('failed', $errorMessage);
        }

        $data = [
            'attribute_name' => $this->request->getPost('attribute_name'),
            'attribute_type' => $this->request->getPost('attribute_type'),
        ];

        if ($id) {
            if (!$this->attributeModel->update($id, $data)) {
                return redirect()->back()->with('failed', 'Failed to update attribute.');
            }
            $message = 'Attribute updated successfully!';
        } else {
            if (!$this->attributeModel->insert($data)) {
                return redirect()->back()->with('failed', 'Failed to create attribute.');
            }
            $message = 'Attribute created successfully!';
        }

        return redirect()->to('/product-attribute')->with('success', $message);
    }


    // DELETE
    public function webDelete($id)
    {
        $this->attributeModel->delete($id);
        return redirect()->to('/product-attribute')->with('success', 'Product attribute deleted successfully.');
    }
}
