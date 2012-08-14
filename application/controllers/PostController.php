<?php
/**
 * User: Vlad Vidican
 * Date: 7/26/12
 * Time: 5:08 PM
 */
class PostController extends Zend_Controller_Action
{
    private $request;
    private $postMapper;
    private $postTextMapper;
    private $postImageMapper;

    public function init()
    {
        $this->request = $this->getRequest();
        $this->postMapper = new Model_PostMapper();
        $this->postTextMapper = new Model_PostTextMapper();
        $this->postImageMapper = new Model_PostImageMapper();
    }

    public function addAction()
    {
        // for the post button in the topbar to be appear as "pressed"
        $this->view->selected = "post";
    }

    public function viewAction()
    {
        // get the id param
        $id = $this->request->getParam('id');
        // get the post with this id
        $post = $this->postMapper->find($id);
        // get all images belonging to this post
        $images = $this->postImageMapper->fetchByPostId($id);
        // get all texts belonging to this post
        $texts = $this->postTextMapper->fetchByPostId($id);
        // data will be the final array sent to the view
        // it's split into 2 elements : 0 and 1, first column and second column
        $data = array(0=>array(),1=>array());
        // set each image in the appropriate column and set its order
        foreach($images as $image)
        {
            $data[$image->getColumn()][$image->getOrder()] = $image;
        }
        // set each text in the appropriate column and set its order
        foreach($texts as $text)
        {
            $data[$text->getColumn()][$text->getOrder()] = $text;
        }
        // send the data and the post to the view
        $this->view->data = $data;
        $this->view->post = $post;
    }

    public function uploadAction()
    {
        // disable the view and the layout, because it's an ajax call
        $this->_helper->layout()->disableLayout();
        Zend_Controller_Front::getInstance()->setParam('noViewRenderer', true);

        // set the path to the images
        $imageThumbsPath = realpath(IMAGES_PATH."/thumbs");
        $imageBigPath = realpath(IMAGES_PATH."/big");
        $imageIconsPath = realpath(IMAGES_PATH."/icons");
        $path = realpath(IMAGES_PATH."/originals")."/";

        // list of valid extensions
        $allowedExtensions = array('jpeg','jpg','png');

        // create the uploader
        $uploader = new qqFileUploader($allowedExtensions);

        // upload the image
        $result = $uploader->handleUpload($path);

        // create the path with the image attached
        $imageOriginalPath = $result['path'];
        $imageThumbsPath .= "/".$result['filename'];
        $imageBigPath .= "/".$result['filename'];
        $imageIconsPath .= "/".$result['filename'];

        // create objects with the original image
        $imageOriginal = WideImage::load($imageOriginalPath);
        $imageThumbs = WideImage::load($imageOriginalPath);
        $imageBig = WideImage::load($imageOriginalPath);
        $imageIcon = WideImage::load($imageOriginalPath);

        // resize the images and save them to the appropriate
        $imageOriginal->resize(1024)->saveToFile($imageOriginalPath);
        $imageBig->resize(560)->saveToFile($imageBigPath);
        $imageThumbs->resize(220)->saveToFile($imageThumbsPath);
        $imageIcon->resize(50)->saveToFile($imageIconsPath);

        // modify results sent to view so that they do not offer to much information
        unset($result['path']);
        $result["file"] = "images/big/".$result["filename"];

        // to pass data through iframe you will need to encode all html tags
        echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
    }

    public function saveAction()
    {
        // disable the view and the layout, because it's an ajax call
        $this->_helper->layout()->disableLayout();
        Zend_Controller_Front::getInstance()->setParam('noViewRenderer', true);

        if($this->request->isPost()){
            $data = $this->request->getParam('data');
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
            $postId = $this->postMapper->save($post);
            foreach($data['elements'] as $key=>$element)
            {
                if($element["type"]=="text"){
                    $postText = new Model_PostText();
                    $postText->setOrder($key);
                    $postText->setPostId($postId);
                    $postText->setText($element["value"]);
                    $postText->setColumn($element['column']);
                    $this->postTextMapper->save($postText);
                }elseif($element["type"]=="image"){
                    $postImage = new Model_PostImage();
                    $postImage->setOrder($key);
                    $postImage->setPostId($postId);
                    $postImage->setName($element["value"]);
                    $postImage->setColumn($element["column"]);
                    $this->postImageMapper->save($postImage);
                }
            }
        }
    }
}
