<?php


class _metabox
{
    private $screen;
    public function __construct($screen)
    {
        $this->screen = $screen;
    }

    public function fillRight($boxes) {
        $this->_fill($boxes,'right');
    }
    public function fillLeft($boxes) {
        $this->_fill($boxes,'left');
    }
    public function addLeft($box) {
        $this->_fill([$box],'left');
    }
    public function addRight($box) {
        $this->_fill([$box],'right');
    }
    public function _fill($boxes,$col) {
        foreach ($boxes as $box) {
            add_meta_box(
                $box->id,                  /* Meta Box ID */
                $box->title,               /* Title */
                $box->content,  /* Function Callback */
                $this->screen,               /* Screen: Our Settings Page */
                $col,                 /* Context */
                'default'
            );
            if ($box->custom_class != '')
            $this->display_meta_box($this->screen,$box->id,$box->custom_class);

        }
    }


    public function display_meta_box($screen,$id,$addClass) {

        // add filter with anonymous callback, default priority and 2 arguments
        add_filter('postbox_classes_'.$screen.'_'.$id, function($classes = []) use ($addClass) {

                array_push($classes, $addClass);

            return $classes;

        });

        // apply new filter with 2 arguments
       // apply_filters('postbox_classes_'.$post->post_type.'_'.$o["args"]["slug"], [], $o["args"]['classes']);
    }
}
class _box
{
    public $id,$title,$content,$custom_class;

    const style_red='meta_box_red';
    const style_green='meta_box_green';
    const style_orange='meta_box_orange';
    const style_blue='meta_box_blue';
    //const style_red='meta_box_red';
    /**
     * _box constructor.
     * @param $id
     * @param $title
     * @param $content
     */
    public function __construct($id, $title, $content,$custom_class = '')
    {
        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
        $this->custom_class = $custom_class;
    }
}