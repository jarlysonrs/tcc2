<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *                                   ATTENTION!
 * If you see this message in your browser (Internet Explorer, Mozilla Firefox, Google Chrome, etc.)
 * this means that PHP is not properly installed on your web server. Please refer to the PHP manual
 * for more details: http://php.net/manual/install.php 
 *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 */

    include_once dirname(__FILE__) . '/components/startup.php';
    include_once dirname(__FILE__) . '/components/application.php';
    include_once dirname(__FILE__) . '/' . 'authorization.php';


    include_once dirname(__FILE__) . '/' . 'database_engine/mysql_engine.php';
    include_once dirname(__FILE__) . '/' . 'components/page/page_includes.php';

    function GetConnectionOptions()
    {
        $result = GetGlobalConnectionOptions();
        $result['client_encoding'] = 'utf8';
        GetApplication()->GetUserAuthentication()->applyIdentityToConnectionOptions($result);
        return $result;
    }

    
    
    
    // OnBeforePageExecute event handler
    
    
    
    class dispositivoPage extends Page
    {
        protected function DoBeforeCreate()
        {
            $this->SetTitle('Problemas');
            $this->SetMenuLabel('Informar problema');
            $this->SetHeader(GetPagesHeader());
            $this->SetFooter(GetPagesFooter());
    
            $this->dataset = new TableDataset(
                MyPDOConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`dispositivo`');
            $this->dataset->addFields(
                array(
                    new IntegerField('id', true, true, true),
                    new IntegerField('tipo'),
                    new StringField('nome'),
                    new StringField('descricao'),
                    new StringField('img'),
                    new StringField('img_mini'),
                    new IntegerField('status')
                )
            );
            $this->dataset->AddLookupField('tipo', 'tipo', new IntegerField('id'), new StringField('tipo', false, false, false, false, 'tipo_tipo', 'tipo_tipo_tipo'), 'tipo_tipo_tipo');
            $this->dataset->AddLookupField('nome', 'setor', new IntegerField('id'), new StringField('setor', false, false, false, false, 'nome_setor', 'nome_setor_setor'), 'nome_setor_setor');
            $this->dataset->AddLookupField('status', '`status`', new IntegerField('id'), new StringField('status', false, false, false, false, 'status_status', 'status_status_status'), 'status_status_status');
        }
    
        protected function DoPrepare() {
    
        }
    
        protected function CreatePageNavigator()
        {
            $result = new CompositePageNavigator($this);
            
            $partitionNavigator = new PageNavigator('pnav', $this, $this->dataset);
            $partitionNavigator->SetRowsPerPage(20);
            $result->AddPageNavigator($partitionNavigator);
            
            return $result;
        }
    
        protected function CreateRssGenerator()
        {
            return null;
        }
    
        protected function setupCharts()
        {
    
        }
    
        protected function getFiltersColumns()
        {
            return array(
                new FilterColumn($this->dataset, 'id', 'id', 'Id'),
                new FilterColumn($this->dataset, 'tipo', 'tipo_tipo', 'Tipo'),
                new FilterColumn($this->dataset, 'nome', 'nome_setor', 'Setor'),
                new FilterColumn($this->dataset, 'descricao', 'descricao', 'Descrição do problema'),
                new FilterColumn($this->dataset, 'img', 'img', 'Imagem'),
                new FilterColumn($this->dataset, 'img_mini', 'img_mini', 'Img Mini'),
                new FilterColumn($this->dataset, 'status', 'status_status', 'Status do problema')
            );
        }
    
        protected function setupQuickFilter(QuickFilter $quickFilter, FixedKeysArray $columns)
        {
            $quickFilter
                ->addColumn($columns['tipo'])
                ->addColumn($columns['nome'])
                ->addColumn($columns['descricao'])
                ->addColumn($columns['img'])
                ->addColumn($columns['status']);
        }
    
        protected function setupColumnFilter(ColumnFilter $columnFilter)
        {
    
        }
    
        protected function setupFilterBuilder(FilterBuilder $filterBuilder, FixedKeysArray $columns)
        {
    
        }
    
        protected function AddOperationsColumns(Grid $grid)
        {
            $actions = $grid->getActions();
            $actions->setCaption($this->GetLocalizerCaptions()->GetMessageString('Actions'));
            $actions->setPosition(ActionList::POSITION_LEFT);
            
            if ($this->GetSecurityInfo()->HasViewGrant())
            {
                $operation = new LinkOperation($this->GetLocalizerCaptions()->GetMessageString('View'), OPERATION_VIEW, $this->dataset, $grid);
                $operation->setUseImage(true);
                $actions->addOperation($operation);
            }
            
            if ($this->GetSecurityInfo()->HasEditGrant())
            {
                $operation = new LinkOperation($this->GetLocalizerCaptions()->GetMessageString('Edit'), OPERATION_EDIT, $this->dataset, $grid);
                $operation->setUseImage(true);
                $actions->addOperation($operation);
                $operation->OnShow->AddListener('ShowEditButtonHandler', $this);
            }
            
            if ($this->GetSecurityInfo()->HasDeleteGrant())
            {
                $operation = new LinkOperation($this->GetLocalizerCaptions()->GetMessageString('Delete'), OPERATION_DELETE, $this->dataset, $grid);
                $operation->setUseImage(true);
                $actions->addOperation($operation);
                $operation->OnShow->AddListener('ShowDeleteButtonHandler', $this);
                $operation->SetAdditionalAttribute('data-modal-operation', 'delete');
                $operation->SetAdditionalAttribute('data-delete-handler-name', $this->GetModalGridDeleteHandler());
            }
            
            if ($this->GetSecurityInfo()->HasAddGrant())
            {
                $operation = new LinkOperation($this->GetLocalizerCaptions()->GetMessageString('Copy'), OPERATION_COPY, $this->dataset, $grid);
                $operation->setUseImage(true);
                $actions->addOperation($operation);
            }
        }
    
        protected function AddFieldColumns(Grid $grid, $withDetails = true)
        {
            //
            // View column for tipo field
            //
            $column = new TextViewColumn('tipo', 'tipo_tipo', 'Tipo', $this->dataset);
            $column->SetOrderable(true);
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for setor field
            //
            $column = new TextViewColumn('nome', 'nome_setor', 'Setor', $this->dataset);
            $column->SetOrderable(true);
            $column->setAlign('left');
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for descricao field
            //
            $column = new TextViewColumn('descricao', 'descricao', 'Descrição do problema', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('dispositivoGrid_descricao_handler_list');
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for img field
            //
            $column = new ExternalImageViewColumn('img', 'img', 'Imagem', $this->dataset);
            $column->SetOrderable(true);
            $column->setSourcePrefixTemplate('imagens/mini_');
            $column->setOriginalImageInfo('img', 'imagens/', '');
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
            
            //
            // View column for status field
            //
            $column = new TextViewColumn('status', 'status_status', 'Status do problema', $this->dataset);
            $column->SetOrderable(true);
            $column->setMinimalVisibility(ColumnVisibility::PHONE);
            $column->SetDescription('');
            $column->SetFixedWidth(null);
            $grid->AddViewColumn($column);
        }
    
        protected function AddSingleRecordViewColumns(Grid $grid)
        {
            //
            // View column for tipo field
            //
            $column = new TextViewColumn('tipo', 'tipo_tipo', 'Tipo', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for setor field
            //
            $column = new TextViewColumn('nome', 'nome_setor', 'Setor', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for descricao field
            //
            $column = new TextViewColumn('descricao', 'descricao', 'Descrição do problema', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('dispositivoGrid_descricao_handler_view');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for img field
            //
            $column = new ExternalImageViewColumn('img', 'img', 'Imagem', $this->dataset);
            $column->SetOrderable(true);
            $column->setSourcePrefixTemplate('imagens/mini_');
            $column->setOriginalImageInfo('img', 'imagens/', '');
            $grid->AddSingleRecordViewColumn($column);
            
            //
            // View column for status field
            //
            $column = new TextViewColumn('status', 'status_status', 'Status do problema', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddSingleRecordViewColumn($column);
        }
    
        protected function AddEditColumns(Grid $grid)
        {
            //
            // Edit column for tipo field
            //
            $editor = new ComboBox('tipo_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $lookupDataset = new TableDataset(
                MyPDOConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`tipo`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('id', true, true, true),
                    new StringField('tipo')
                )
            );
            $lookupDataset->setOrderByField('tipo', 'ASC');
            $editColumn = new LookUpEditColumn(
                'Tipo', 
                'tipo', 
                $editor, 
                $this->dataset, 'id', 'tipo', $lookupDataset);
            $editColumn->SetAllowSetToNull(true);
            $editColumn->setAllowListCellEdit(false);
            $editColumn->setAllowSingleViewCellEdit(false);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for nome field
            //
            $editor = new ComboBox('nome_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $lookupDataset = new TableDataset(
                MyPDOConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`setor`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('id', true, true, true),
                    new StringField('setor')
                )
            );
            $lookupDataset->setOrderByField('setor', 'ASC');
            $editColumn = new LookUpEditColumn(
                'Setor', 
                'nome', 
                $editor, 
                $this->dataset, 'id', 'setor', $lookupDataset);
            $editColumn->SetAllowSetToNull(true);
            $editColumn->setAllowListCellEdit(false);
            $editColumn->setAllowSingleViewCellEdit(false);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for descricao field
            //
            $editor = new TextEdit('descricao_edit');
            $editor->SetMaxLength(100);
            $editColumn = new CustomEditColumn('Descrição do problema', 'descricao', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $editColumn->setAllowListCellEdit(false);
            $editColumn->setAllowSingleViewCellEdit(false);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for img field
            //
            $editor = new ImageUploader('img_edit');
            $editor->SetShowImage(true);
            $editor->setAcceptableFileTypes('image/*');
            $editColumn = new UploadFileToFolderColumn('Imagem', 'img', $editor, $this->dataset, false, false, 'imagens/', '%original_file_name%', $this->OnFileUpload, true);
            $editColumn->SetReplaceUploadedFileIfExist(true);
            $editColumn->SetGenerationImageThumbnails(
                'img_mini',
                'imagens/',
                Delegate::CreateFromMethod($this, 'img_Thumbnail_GenerateFileName_edit'),
                new ImageFitByWidthResizeFilter(250),
                true
            );
            $editColumn->SetAllowSetToNull(true);
            $editColumn->setAllowListCellEdit(false);
            $editColumn->setAllowSingleViewCellEdit(false);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
            
            //
            // Edit column for status field
            //
            $editor = new ComboBox('status_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $lookupDataset = new TableDataset(
                MyPDOConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`status`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('id', true, true, true),
                    new StringField('status')
                )
            );
            $lookupDataset->setOrderByField('status', 'ASC');
            $editColumn = new LookUpEditColumn(
                'Status do problema', 
                'status', 
                $editor, 
                $this->dataset, 'id', 'status', $lookupDataset);
            $editColumn->SetAllowSetToNull(true);
            $editColumn->setAllowListCellEdit(false);
            $editColumn->setAllowSingleViewCellEdit(false);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddEditColumn($editColumn);
        }
    
        protected function AddMultiEditColumns(Grid $grid)
        {
            //
            // Edit column for tipo field
            //
            $editor = new ComboBox('tipo_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $lookupDataset = new TableDataset(
                MyPDOConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`tipo`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('id', true, true, true),
                    new StringField('tipo')
                )
            );
            $lookupDataset->setOrderByField('tipo', 'ASC');
            $editColumn = new LookUpEditColumn(
                'Tipo', 
                'tipo', 
                $editor, 
                $this->dataset, 'id', 'tipo', $lookupDataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
            
            //
            // Edit column for nome field
            //
            $editor = new ComboBox('nome_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $lookupDataset = new TableDataset(
                MyPDOConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`setor`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('id', true, true, true),
                    new StringField('setor')
                )
            );
            $lookupDataset->setOrderByField('setor', 'ASC');
            $editColumn = new LookUpEditColumn(
                'Setor', 
                'nome', 
                $editor, 
                $this->dataset, 'id', 'setor', $lookupDataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
            
            //
            // Edit column for descricao field
            //
            $editor = new TextEdit('descricao_edit');
            $editor->SetMaxLength(100);
            $editColumn = new CustomEditColumn('Descrição do problema', 'descricao', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
            
            //
            // Edit column for img field
            //
            $editor = new ImageUploader('img_edit');
            $editor->SetShowImage(true);
            $editor->setAcceptableFileTypes('image/*');
            $editColumn = new UploadFileToFolderColumn('Imagem', 'img', $editor, $this->dataset, false, false, 'imagens/', '%original_file_name%', $this->OnFileUpload, true);
            $editColumn->SetReplaceUploadedFileIfExist(true);
            $editColumn->SetGenerationImageThumbnails(
                'img_mini',
                'imagens/',
                Delegate::CreateFromMethod($this, 'img_Thumbnail_GenerateFileName_multi_edit'),
                new ImageFitByWidthResizeFilter(250),
                true
            );
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
            
            //
            // Edit column for status field
            //
            $editor = new ComboBox('status_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $lookupDataset = new TableDataset(
                MyPDOConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`status`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('id', true, true, true),
                    new StringField('status')
                )
            );
            $lookupDataset->setOrderByField('status', 'ASC');
            $editColumn = new LookUpEditColumn(
                'Status do problema', 
                'status', 
                $editor, 
                $this->dataset, 'id', 'status', $lookupDataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddMultiEditColumn($editColumn);
        }
    
        protected function AddInsertColumns(Grid $grid)
        {
            //
            // Edit column for tipo field
            //
            $editor = new ComboBox('tipo_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $lookupDataset = new TableDataset(
                MyPDOConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`tipo`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('id', true, true, true),
                    new StringField('tipo')
                )
            );
            $lookupDataset->setOrderByField('tipo', 'ASC');
            $editColumn = new LookUpEditColumn(
                'Tipo', 
                'tipo', 
                $editor, 
                $this->dataset, 'id', 'tipo', $lookupDataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for nome field
            //
            $editor = new ComboBox('nome_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $lookupDataset = new TableDataset(
                MyPDOConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`setor`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('id', true, true, true),
                    new StringField('setor')
                )
            );
            $lookupDataset->setOrderByField('setor', 'ASC');
            $editColumn = new LookUpEditColumn(
                'Setor', 
                'nome', 
                $editor, 
                $this->dataset, 'id', 'setor', $lookupDataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for descricao field
            //
            $editor = new TextEdit('descricao_edit');
            $editor->SetMaxLength(100);
            $editColumn = new CustomEditColumn('Descrição do problema', 'descricao', $editor, $this->dataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for img field
            //
            $editor = new ImageUploader('img_edit');
            $editor->SetShowImage(true);
            $editor->setAcceptableFileTypes('image/*');
            $editColumn = new UploadFileToFolderColumn('Imagem', 'img', $editor, $this->dataset, false, false, 'imagens/', '%original_file_name%', $this->OnFileUpload, true);
            $editColumn->SetReplaceUploadedFileIfExist(true);
            $editColumn->SetGenerationImageThumbnails(
                'img_mini',
                'imagens/',
                Delegate::CreateFromMethod($this, 'img_Thumbnail_GenerateFileName_insert'),
                new ImageFitByWidthResizeFilter(250),
                true
            );
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            
            //
            // Edit column for status field
            //
            $editor = new ComboBox('status_edit', $this->GetLocalizerCaptions()->GetMessageString('PleaseSelect'));
            $lookupDataset = new TableDataset(
                MyPDOConnectionFactory::getInstance(),
                GetConnectionOptions(),
                '`status`');
            $lookupDataset->addFields(
                array(
                    new IntegerField('id', true, true, true),
                    new StringField('status')
                )
            );
            $lookupDataset->setOrderByField('status', 'ASC');
            $editColumn = new LookUpEditColumn(
                'Status do problema', 
                'status', 
                $editor, 
                $this->dataset, 'id', 'status', $lookupDataset);
            $editColumn->SetAllowSetToNull(true);
            $this->ApplyCommonColumnEditProperties($editColumn);
            $grid->AddInsertColumn($editColumn);
            $grid->SetShowAddButton(true && $this->GetSecurityInfo()->HasAddGrant());
        }
    
        private function AddMultiUploadColumn(Grid $grid)
        {
    
        }
    
        protected function AddPrintColumns(Grid $grid)
        {
            //
            // View column for tipo field
            //
            $column = new TextViewColumn('tipo', 'tipo_tipo', 'Tipo', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
            
            //
            // View column for setor field
            //
            $column = new TextViewColumn('nome', 'nome_setor', 'Setor', $this->dataset);
            $column->SetOrderable(true);
            $column->setAlign('left');
            $grid->AddPrintColumn($column);
            
            //
            // View column for descricao field
            //
            $column = new TextViewColumn('descricao', 'descricao', 'Descrição do problema', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('dispositivoGrid_descricao_handler_print');
            $grid->AddPrintColumn($column);
            
            //
            // View column for img field
            //
            $column = new ExternalImageViewColumn('img', 'img', 'Imagem', $this->dataset);
            $column->SetOrderable(true);
            $column->setSourcePrefixTemplate('imagens/mini_');
            $column->setOriginalImageInfo('img', 'imagens/', '');
            $grid->AddPrintColumn($column);
            
            //
            // View column for status field
            //
            $column = new TextViewColumn('status', 'status_status', 'Status do problema', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddPrintColumn($column);
        }
    
        protected function AddExportColumns(Grid $grid)
        {
            //
            // View column for tipo field
            //
            $column = new TextViewColumn('tipo', 'tipo_tipo', 'Tipo', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
            
            //
            // View column for setor field
            //
            $column = new TextViewColumn('nome', 'nome_setor', 'Setor', $this->dataset);
            $column->SetOrderable(true);
            $column->setAlign('left');
            $grid->AddExportColumn($column);
            
            //
            // View column for descricao field
            //
            $column = new TextViewColumn('descricao', 'descricao', 'Descrição do problema', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('dispositivoGrid_descricao_handler_export');
            $grid->AddExportColumn($column);
            
            //
            // View column for img field
            //
            $column = new ExternalImageViewColumn('img', 'img', 'Imagem', $this->dataset);
            $column->SetOrderable(true);
            $column->setSourcePrefixTemplate('imagens/mini_');
            $column->setOriginalImageInfo('img', 'imagens/', '');
            $grid->AddExportColumn($column);
            
            //
            // View column for status field
            //
            $column = new TextViewColumn('status', 'status_status', 'Status do problema', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddExportColumn($column);
        }
    
        private function AddCompareColumns(Grid $grid)
        {
            //
            // View column for tipo field
            //
            $column = new TextViewColumn('tipo', 'tipo_tipo', 'Tipo', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddCompareColumn($column);
            
            //
            // View column for setor field
            //
            $column = new TextViewColumn('nome', 'nome_setor', 'Setor', $this->dataset);
            $column->SetOrderable(true);
            $column->setAlign('left');
            $grid->AddCompareColumn($column);
            
            //
            // View column for descricao field
            //
            $column = new TextViewColumn('descricao', 'descricao', 'Descrição do problema', $this->dataset);
            $column->SetOrderable(true);
            $column->SetMaxLength(75);
            $column->SetFullTextWindowHandlerName('dispositivoGrid_descricao_handler_compare');
            $grid->AddCompareColumn($column);
            
            //
            // View column for img field
            //
            $column = new ExternalImageViewColumn('img', 'img', 'Imagem', $this->dataset);
            $column->SetOrderable(true);
            $column->setSourcePrefixTemplate('imagens/mini_');
            $column->setOriginalImageInfo('img', 'imagens/', '');
            $grid->AddCompareColumn($column);
            
            //
            // View column for status field
            //
            $column = new TextViewColumn('status', 'status_status', 'Status do problema', $this->dataset);
            $column->SetOrderable(true);
            $grid->AddCompareColumn($column);
        }
    
        private function AddCompareHeaderColumns(Grid $grid)
        {
    
        }
    
        public function GetPageDirection()
        {
            return null;
        }
    
        public function isFilterConditionRequired()
        {
            return false;
        }
    
        protected function ApplyCommonColumnEditProperties(CustomEditColumn $column)
        {
            $column->SetDisplaySetToNullCheckBox(false);
            $column->SetDisplaySetToDefaultCheckBox(false);
    		$column->SetVariableContainer($this->GetColumnVariableContainer());
        }
    
        public function img_Thumbnail_GenerateFileName_insert(&$filepath, &$handled, $original_file_name, $original_file_extension, $file_size)
        {
        $targetFolder = FormatDatasetFieldsTemplate($this->GetDataset(), 'imagens/');
        FileUtils::ForceDirectories($targetFolder);
        
        $filename = ApplyVariablesMapToTemplate('mini_%original_file_name%',
            array(
                'original_file_name' => $original_file_name,
                'original_file_extension' => $original_file_extension,
                'file_size' => $file_size
            )
        );
        $filepath = Path::Combine($targetFolder, $filename);
        
        $handled = true;
        }
        
        function GetCustomClientScript()
        {
            return ;
        }
        
        function GetOnPageLoadedClientScript()
        {
            return ;
        }
        public function img_Thumbnail_GenerateFileName_edit(&$filepath, &$handled, $original_file_name, $original_file_extension, $file_size)
        {
        $targetFolder = FormatDatasetFieldsTemplate($this->GetDataset(), 'imagens/');
        FileUtils::ForceDirectories($targetFolder);
        
        $filename = ApplyVariablesMapToTemplate('mini_%original_file_name%',
            array(
                'original_file_name' => $original_file_name,
                'original_file_extension' => $original_file_extension,
                'file_size' => $file_size
            )
        );
        $filepath = Path::Combine($targetFolder, $filename);
        
        $handled = true;
        }
        public function img_Thumbnail_GenerateFileName_multi_edit(&$filepath, &$handled, $original_file_name, $original_file_extension, $file_size)
        {
        $targetFolder = FormatDatasetFieldsTemplate($this->GetDataset(), 'imagens/');
        FileUtils::ForceDirectories($targetFolder);
        
        $filename = ApplyVariablesMapToTemplate('mini_%original_file_name%',
            array(
                'original_file_name' => $original_file_name,
                'original_file_extension' => $original_file_extension,
                'file_size' => $file_size
            )
        );
        $filepath = Path::Combine($targetFolder, $filename);
        
        $handled = true;
        }
        
        protected function GetEnableModalGridDelete() { return true; }
    
        protected function CreateGrid()
        {
            $result = new Grid($this, $this->dataset);
            if ($this->GetSecurityInfo()->HasDeleteGrant())
               $result->SetAllowDeleteSelected(false);
            else
               $result->SetAllowDeleteSelected(false);   
            
            ApplyCommonPageSettings($this, $result);
            
            $result->SetUseImagesForActions(true);
            $result->SetUseFixedHeader(false);
            $result->SetShowLineNumbers(false);
            $result->SetShowKeyColumnsImagesInHeader(false);
            $result->setAllowSortingByDialog(false);
            $result->SetViewMode(ViewMode::TABLE);
            $result->setEnableRuntimeCustomization(false);
            $result->setAllowAddMultipleRecords(false);
            $result->setMultiEditAllowed($this->GetSecurityInfo()->HasEditGrant() && false);
            $result->setTableBordered(false);
            $result->setTableCondensed(false);
            
            $result->SetHighlightRowAtHover(false);
            $result->SetWidth('');
            $this->AddOperationsColumns($result);
            $this->AddFieldColumns($result);
            $this->AddSingleRecordViewColumns($result);
            $this->AddEditColumns($result);
            $this->AddMultiEditColumns($result);
            $this->AddInsertColumns($result);
            $this->AddPrintColumns($result);
            $this->AddExportColumns($result);
            $this->AddMultiUploadColumn($result);
    
    
            $this->SetShowPageList(true);
            $this->SetShowTopPageNavigator(true);
            $this->SetShowBottomPageNavigator(true);
            $this->setPrintListAvailable(false);
            $this->setPrintListRecordAvailable(false);
            $this->setPrintOneRecordAvailable(false);
            $this->setAllowPrintSelectedRecords(false);
            $this->setExportListAvailable(array());
            $this->setExportSelectedRecordsAvailable(array());
            $this->setExportListRecordAvailable(array());
            $this->setExportOneRecordAvailable(array());
    
            return $result;
        }
     
        protected function setClientSideEvents(Grid $grid) {
    
        }
    
        protected function doRegisterHandlers() {
            //
            // View column for descricao field
            //
            $column = new TextViewColumn('descricao', 'descricao', 'Descrição do problema', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'dispositivoGrid_descricao_handler_list', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for descricao field
            //
            $column = new TextViewColumn('descricao', 'descricao', 'Descrição do problema', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'dispositivoGrid_descricao_handler_print', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for descricao field
            //
            $column = new TextViewColumn('descricao', 'descricao', 'Descrição do problema', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'dispositivoGrid_descricao_handler_compare', $column);
            GetApplication()->RegisterHTTPHandler($handler);
            
            //
            // View column for descricao field
            //
            $column = new TextViewColumn('descricao', 'descricao', 'Descrição do problema', $this->dataset);
            $column->SetOrderable(true);
            $handler = new ShowTextBlobHandler($this->dataset, $this, 'dispositivoGrid_descricao_handler_view', $column);
            GetApplication()->RegisterHTTPHandler($handler);
        }
       
        protected function doCustomRenderColumn($fieldName, $fieldData, $rowData, &$customText, &$handled)
        { 
    
        }
    
        protected function doCustomRenderPrintColumn($fieldName, $fieldData, $rowData, &$customText, &$handled)
        { 
    
        }
    
        protected function doCustomRenderExportColumn($exportType, $fieldName, $fieldData, $rowData, &$customText, &$handled)
        { 
    
        }
    
        protected function doCustomDrawRow($rowData, &$cellFontColor, &$cellFontSize, &$cellBgColor, &$cellItalicAttr, &$cellBoldAttr)
        {
    
        }
    
        protected function doExtendedCustomDrawRow($rowData, &$rowCellStyles, &$rowStyles, &$rowClasses, &$cellClasses)
        {
    
        }
    
        protected function doCustomRenderTotal($totalValue, $aggregate, $columnName, &$customText, &$handled)
        {
    
        }
    
        protected function doCustomDefaultValues(&$values, &$handled) 
        {
    
        }
    
        protected function doCustomCompareColumn($columnName, $valueA, $valueB, &$result)
        {
    
        }
    
        protected function doBeforeInsertRecord($page, &$rowData, $tableName, &$cancel, &$message, &$messageDisplayTime)
        {
    
        }
    
        protected function doBeforeUpdateRecord($page, $oldRowData, &$rowData, $tableName, &$cancel, &$message, &$messageDisplayTime)
        {
    
        }
    
        protected function doBeforeDeleteRecord($page, &$rowData, $tableName, &$cancel, &$message, &$messageDisplayTime)
        {
    
        }
    
        protected function doAfterInsertRecord($page, $rowData, $tableName, &$success, &$message, &$messageDisplayTime)
        {
    
        }
    
        protected function doAfterUpdateRecord($page, $oldRowData, $rowData, $tableName, &$success, &$message, &$messageDisplayTime)
        {
    
        }
    
        protected function doAfterDeleteRecord($page, $rowData, $tableName, &$success, &$message, &$messageDisplayTime)
        {
    
        }
    
        protected function doCustomHTMLHeader($page, &$customHtmlHeaderText)
        { 
    
        }
    
        protected function doGetCustomTemplate($type, $part, $mode, &$result, &$params)
        {
    
        }
    
        protected function doGetCustomExportOptions(Page $page, $exportType, $rowData, &$options)
        {
    
        }
    
        protected function doFileUpload($fieldName, $rowData, &$result, &$accept, $originalFileName, $originalFileExtension, $fileSize, $tempFileName)
        {
    
        }
    
        protected function doPrepareChart(Chart $chart)
        {
    
        }
    
        protected function doPrepareColumnFilter(ColumnFilter $columnFilter)
        {
    
        }
    
        protected function doPrepareFilterBuilder(FilterBuilder $filterBuilder, FixedKeysArray $columns)
        {
    
        }
    
        protected function doGetSelectionFilters(FixedKeysArray $columns, &$result)
        {
    
        }
    
        protected function doGetCustomFormLayout($mode, FixedKeysArray $columns, FormLayout $layout)
        {
    
        }
    
        protected function doGetCustomColumnGroup(FixedKeysArray $columns, ViewColumnGroup $columnGroup)
        {
    
        }
    
        protected function doPageLoaded()
        {
    
        }
    
        protected function doCalculateFields($rowData, $fieldName, &$value)
        {
    
        }
    
        protected function doGetCustomPagePermissions(Page $page, PermissionSet &$permissions, &$handled)
        {
    
        }
    
        protected function doGetCustomRecordPermissions(Page $page, &$usingCondition, $rowData, &$allowEdit, &$allowDelete, &$mergeWithDefault, &$handled)
        {
    
        }
    
    }

    SetUpUserAuthorization();

    try
    {
        $Page = new dispositivoPage("dispositivo", "dispositivo.php", GetCurrentUserPermissionSetForDataSource("dispositivo"), 'UTF-8');
        $Page->SetRecordPermission(GetCurrentUserRecordPermissionsForDataSource("dispositivo"));
        GetApplication()->SetMainPage($Page);
        GetApplication()->Run();
    }
    catch(Exception $e)
    {
        ShowErrorPage($e);
    }
	
