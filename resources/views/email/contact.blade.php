@extends('email.master')
@section('emailContent')
    <table class="layout layout--no-gutter" style="border-collapse: collapse;table-layout: fixed;Margin-left: auto;Margin-right: auto;overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;background-color: #ffffff;" align="center">
        <tbody>
        <tr>
            <td class="column" style="padding: 0;text-align: left;vertical-align: top;color: #565656;font-size: 14px;line-height: 21px;font-family: Bitter,Georgia,serif;width: 600px;">
                <div style="Margin-left: 20px;Margin-right: 20px;Margin-top: 12px;">
                    <div style="font-size: 20px;line-height: 20px;mso-line-height-rule: exactly;">&nbsp;</div>
                    <p style="Margin-top: 20px;Margin-bottom: 0;">Nội dung đính kèm</p>
                    <ol style="Margin-top: 20px;Margin-bottom: 0;Margin-left: 24px;padding: 0; list-style: none">
                        <li style="Margin-top: 20px;Margin-bottom: 0;">
                            {{ isset($name) && !empty($name) ? 'Họ và tên : '. $name : '' }}
                        </li>
                        <li style="Margin-top:0px;Margin-bottom: 0;">
                            {{ isset($email) && !empty($email) ? 'Email : '. $email : '' }}
                        </li>
                        <li style="Margin-top: 0;Margin-bottom: 0;">
                            {{ isset($phone) && !empty($phone) ? 'Số điện thoại : '. $phone : '' }}
                        </li>
                        <li style="Margin-top: 0;Margin-bottom: 0;">
                            {{ isset($content) && !empty($content) ? 'Nội dung : '. $content : '' }}
                        </li>
                    </ol>
                    <br><br>
                </div>
                <div style="Margin-left: 20px;Margin-right: 20px;Margin-bottom: 12px;">
                    <div class="divider" style="display: block;font-size: 2px;line-height: 2px;width: 40px;background-color: #c8c8c8;Margin-left: 260px;Margin-right: 260px;">&nbsp;</div>
                </div>
            </td>
        </tr>
        </tbody>
    </table>

@stop