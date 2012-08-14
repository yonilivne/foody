<?php
/**
 * Created by IntelliJ IDEA.
 * User: vladvidican
 * Date: 7/26/12
 * Time: 5:08 PM
 * To change this template use File | Settings | File Templates.
 */
class PostController extends Zend_Controller_Action
{
    public function init()
    {

    }

    public function addAction()
    {
         $this->view->selected = "post";
    }

    public function viewAction()
    {
        $request = $this->getRequest();
        $id = $request->getParam('id');
        $postImageMapper = new Model_PostImageMapper();
        $postTextMapper = new Model_PostTextMapper();
        $postMapper = new Model_PostMapper();
        $post = $postMapper->find($id);
        $images = $postImageMapper->fetchByPostId($id);
        $texts = $postTextMapper->fetchByPostId($id);
        $data = array(0=>array(),1=>array());
        foreach($images as $image)
        {
            $data[$image->getColumn()][$image->getOrder()] = $image;
        }
        foreach($texts as $text)
        {
            $data[$text->getColumn()][$text->getOrder()] = $text;
        }
        $this->view->data = $data;
        $this->view->post = $post;
    }

    public function uploadAction()
    {
        $this->_helper->layout()->disableLayout();
        Zend_Controller_Front::getInstance()->setParam('noViewRenderer', true);
        $imageThumbsPath = realpath(PICS_PATH."/thumbs");
        $imageThumbsIndexPath = realpath(PICS_PATH."/thumbs_index");

        // list of valid extensions, ex. array("jpeg", "xml", "bmp")
        $allowedExtensions = array('jpeg','jpg','png');

        $uploader = new qqFileUploader($allowedExtensions);
        $path = realpath(PICS_PATH."/originals")."/";
        $result = $uploader->handleUpload($path);

        $imageOriginalPath = $result['path'];
        $imageThumbsPath .= "/".$result['filename'];
        $imageThumbsIndexPath .= "/".$result['filename'];
        $imageOriginal = WideImage::load($imageOriginalPath);
        $imageThumbs = WideImage::load($imageOriginalPath);
        $imageThumbsIndex = WideImage::load($imageOriginalPath);
        $imageOriginal->resize(1024)->saveToFile($imageOriginalPath);
        $imageThumbs->resize(560)->saveToFile($imageThumbsPath);
        $imageThumbsIndex->resize(220)->saveToFile($imageThumbsIndexPath);

        // to pass data through iframe you will need to encode all html tags
        echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
    }

    public function saveAction()
    {
        $this->_helper->layout()->disableLayout();
        Zend_Controller_Front::getInstance()->setParam('noViewRenderer', true);
        $request = $this->getRequest();
        if($request->isPost()){
            $data = $request->getParam('data');
            $first = false;
            $description = null;
            foreach($data['elements'] as $element){
                if($element['type']=="text" && !$first)
                {
                    $description = $element['value'];
                    $first = true;
                }
            }
            $first = false;
            $thumb = null;
            foreach($data['elements'] as $element){
                if($element['type']=="image" && !$first)
                {
                    $thumb = $element['value'];
                    $first = true;
                }
            }
            $post = new Model_Post();
            $post->setTitle($data['title']);
            $post->setDescription($description);
            $post->setThumb($thumb);
            $postMapper = new Model_PostMapper();
            $postTextMapper = new Model_PostTextMapper();
            $postImageMapper = new Model_PostImageMapper();
            $postId = $postMapper->save($post);
            foreach($data['elements'] as $key=>$element)
            {
                if($element["type"]=="text"){
                    $postText = new Model_PostText();
                    $postText->setOrder($key);
                    $postText->setPostId($postId);
                    $postText->setText($element["value"]);
                    $postText->setColumn($element['column']);
                    $postTextMapper->save($postText);
                }elseif($element["type"]=="image"){
                    $postImage = new Model_PostImage();
                    $postImage->setOrder($key);
                    $postImage->setPostId($postId);
                    $postImage->setName($element["value"]);
                    $postImage->setColumn($element["column"]);
                    $postImageMapper->save($postImage);
                }
            }
        }
    }
}
