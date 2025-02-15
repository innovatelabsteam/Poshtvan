<?php
namespace poshtvan\app;
class notice
{
    private static $_notices=[];
    private static $_cookies;
    private const COOKIE_KEY_NAME = 'poshtvan_notices';

    private static function getCoockie()
    {
        if(!self::$_cookies)
        {
            self::$_cookies = isset($_COOKIE[self::COOKIE_KEY_NAME]) ? $_COOKIE[self::COOKIE_KEY_NAME] : false;
        }
        return unserialize(base64_decode(self::$_cookies));
    }
    private static function updateCookie($data)
    {
        return setcookie(self::COOKIE_KEY_NAME, base64_encode(serialize($data)), time() + DAY_IN_SECONDS);
    }

    static function add_notice($code, $notice, $type='error', $driver = 'tmp')
    {
        if($driver == 'cookie')
        {
            $notices = self::getCoockie();
            if(!$notices)
            {
                $notices = [];
            }
            $notices[$code][] = [
                'notice' => $notice,
                'type' => $type,
            ];
            return self::updateCookie($notices);
        }else{
            self::$_notices[$code][] = [
                'notice' => $notice,
                'type' => $type
            ];
            return self::$_notices;
        }
    }
    static function show_notices($code, $driver='tmp')
    {
        if($driver == 'cookie')
        {
            $notices = self::getCoockie();
            if(!$notices || !isset($notices[$code]))
            {
                return false;
            }
            foreach($notices[$code] as $notice)
            {
                echo '<span class="alert '.esc_html($notice['type']).'">'.esc_html($notice['notice']).'</span>';
            }
            unset($notices[$code]);
            self::updateCookie($notices);
            return;
        }
        if(!self::$_notices || !isset(self::$_notices[$code]))
        {
            return false;
        }
        foreach(self::$_notices[$code] as $notice)
        {
            echo '<span class="alert '.esc_html($notice['type']).'">'.esc_html($notice['notice']).'</span>';
        }
    }

    static function renderAdminNotice($noticeText, $args=[])
    {
        $wrapperStyles = [
            "position: relative",
            "margin: 20px 0",
            "width: 100%",
            "max-width:100%",
            "font-family:IRANSans",
            "font-size: 16px",
            "box-sizing: border-box",
            "border-radius: 10px",
            "font-weight: 100",
            'display: flex',
            'align-items: center',
            'gap: 25px',
            'padding: 30px 20px',
            'background: white',
            'border: 1px solid #e4e4e4',
            'color: black',
        ];
        $logoWrapperStyle = [
            'font-size: 25px',
            'font-weight: bold',
        ];
        $logoImgStyle = [
            'border-radius: 50%',
            'width: 65px',
        ];

        $noticeTextStyle = [
            'flex: auto',
        ];
        $noticeBoxLine = [
            'position: absolute',
            'width: 5px',
            'height: 85%',
            'background-color: #d70150',
            'border-radius: 20px',
        ];

        $btnStyles = [
            'box-shadow: none',
            'color:#d70150',
            'text-decoration:none',
            'border-radius:5px',
            'padding:10px 20px',
            'background-color: #f8eef1',
            'font-weight: bold',
            'text-wrap: nowrap',
        ];

        if (is_rtl()) {
            $noticeBoxLine[] = 'right: 0';
            $logoWrapperStyle[] = 'border-left: 2px solid #e4e3e3';
            $logoWrapperStyle[] = 'padding-left: 20px';
        } else {
            $noticeBoxLine[] = 'left: 0';
            $logoWrapperStyle[] = 'border-right: 2px solid #e4e3e3';
            $logoWrapperStyle[] = 'padding-right: 20px';
        }

        $wrapperStyles = implode(';', $wrapperStyles);
        $logoWrapperStyle = implode(';', $logoWrapperStyle);
        $logoImgStyle = implode(';', $logoImgStyle);
        $noticeTextStyle = implode(';', $noticeTextStyle);
        $noticeBoxLine = implode(';', $noticeBoxLine);
        $btnStyles = implode(';', $btnStyles);
        ?>

        <div class="wrap">
            <div class="mw-poshtvan-admin-notice" style="<?php echo esc_attr($wrapperStyles) ?>">
                <span style="<?php echo esc_attr($noticeBoxLine) ?>"></span>
                <span style="<?php echo esc_attr($logoWrapperStyle)?>"><img style="<?php echo esc_attr($logoImgStyle)?>" src="<?php echo esc_url(\poshtvan\app\assets::get_img_url('circle-logo', 'svg'))?>" alt="poshtvan-logo"></span>

                <span style="<?php echo esc_attr($noticeTextStyle) ?>"><?php echo esc_html($noticeText); ?></span>
                <span class="mw-notice-buttons">
                    <?php if(isset($args['link']) && $args['link']): ?>
                        <a style="<?php echo esc_attr($btnStyles) ?>" href="<?php echo esc_url($args['link']) ?>"><?php echo isset($args['link_text']) && $args['link_text'] ? esc_html($args['link_text']) : esc_html__('Get Started', 'poshtvan'); ?></a>
                    <?php endif; ?>
                </span>
            </div>
        </div>
        <?php
    }
}