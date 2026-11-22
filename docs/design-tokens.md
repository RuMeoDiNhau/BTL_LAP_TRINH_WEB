# Design tokens: trang About và FAQs (tham khảo mẫu Decor)

Nguồn: giá trị khai báo đọc từ About.html, FAQs.html và CSS của mẫu (chưa phải giá trị computed).
File này chỉ ghi giá trị và cách bố cục, không ghi selector hay rule của mẫu.
Khi dựng: tự đặt tên class và biến riêng, ưu tiên utility của Bootstrap 5.3.3, chỉ viết CSS riêng cho phần Bootstrap không có.

## 1. Màu

| Tên đề xuất | Giá trị | Dùng cho |
|---|---|---|
| color-text | #8f8f8f | chữ thân bài |
| color-heading | #212529 | tiêu đề, chữ câu hỏi accordion |
| color-ink | #111 (rgb 17,17,17) | màu link |
| color-accent | #000 | nền nút chính, chữ accordion khi mở |
| color-accent-hover | #6c757d | nền nút khi hover/active |
| color-surface | #F1F1F0 | nền khối story (bg-light) |
| color-muted | #9aa1a7 | khai báo trong mẫu, chưa rõ dùng ở đâu |
| color-muted-dark | #51565b | khai báo trong mẫu, chưa rõ dùng ở đâu |
| color-teal | #5b878a | khai báo trong mẫu, chưa thấy dùng ở 2 trang; bỏ nếu không cần |
| color-on-accent | #fff | chữ trên nút chính |
| focus-border | #ccc | viền ô nhập khi focus |
| focus-ring | rgba(200,200,200,.25), rộng 0.25rem | bóng khi focus |

## 2. Typography

| Phần tử | Giá trị |
|---|---|
| Tiêu đề (h1-h6) | Playfair Display, weight 400, line-height 1.24, màu color-heading |
| Thân bài | Jost, 1rem, weight 400, line-height 1.7, màu color-text |
| Thân bài khi màn hình rộng tối đa 991px | 1.2rem |
| Tiêu đề trang (h1) | class display-5 fw-bold |
| Tiêu đề khối (h3 FAQ) | fw-bold |
| Câu hỏi accordion | fs-5 |
| Menu | uppercase, fs-6 |
| Nút | 16px, weight 500, uppercase |
| Trích dẫn testimonial | 2.5em (trong slider của mẫu) |
| Tên người đánh giá | 14px, uppercase |
| Tiêu đề khối testimonial | 1em, bold, line-height 1 |
| Transition | link 0.3s ease-out; nút 0.5s ease-in-out |

## 3. Khoảng cách và kích thước

| Tên | Giá trị | Dùng cho |
|---|---|---|
| section-sm | padding trên/dưới 2em | khối nhỏ (nếu cần) |
| section-md | padding trên/dưới 7em | story, features, footer |
| section-lg | padding trên/dưới 10em | tiêu đề trang (kèm margin-top mt-5) |
| container-fluid | max-width 1800px | features, footer |
| Khối chữ trong story | p-5 | cột chữ |
| Hàng story thứ hai | py-5 | thêm khoảng cách |
| Khối feature | py-5, icon 40x40 | mỗi feature |
| Gap FAQ | g-lg-5 | hai cột FAQ ở màn hình lớn |
| accordion-item | mt-3 | giữa các câu hỏi |
| Breadcrumb | khoảng cách ngang mỗi mục 1em | |

## 4. Thành phần

**Nút chính**: padding 0.8rem (dọc) x 2.2rem (ngang), bo góc 5px, viền 2px, nền #000, chữ #fff, hover nền #6c757d, uppercase.

**Accordion**: không viền, bo góc 0, nền trong suốt. Nút câu hỏi padding dọc 1rem, ngang 0, chữ color-heading. Khi mở: chữ color-accent. Thân câu trả lời padding dọc 0.5rem, ngang 0, không bóng khi focus. Biểu tượng mũi tên: mẫu tải từ địa chỉ ngoài, không dùng; dùng biểu tượng mặc định của Bootstrap.

**Breadcrumb**: Home / (bỏ "Pages") / trang hiện tại, mục cuối aria-current="page".

**Ô nhập**: viền và bóng focus theo mục 1.

**Navbar**: cố định ở đầu trang (fixed-top), viền dưới, chữ uppercase fs-6, padding p-3, thu gọn thành menu trượt (offcanvas) dưới 992px.

## 5. Bố cục theo trang (lưới Bootstrap)

**About**
1. Tiêu đề trang: section-lg + mt-5, container, căn giữa theo cột (flex-column), h1 + breadcrumb.
2. Giới thiệu: container, 3 cột col-md-4, mỗi cột một đoạn văn.
3. Story (section-md): hai hàng nền color-surface, căn giữa dọc (align-items-center). Hàng 1: ảnh col-md-6 bên trái, chữ col-md-6 bên phải (h2 + đoạn văn, p-5). Hàng 2: đảo ngược, chữ bên trái, ảnh bên phải.
4. Testimonials: tiêu đề h3 căn giữa, 4 trích dẫn căn giữa. Mẫu dùng slider 3D; bản của bạn dùng lưới tĩnh 4 ô.
5. Features (section-md): container-fluid, hàng 4 ô (col-md-2 mỗi ô, ô đầu offset-md-1), icon 40px + h5 viết hoa chữ cái đầu (text-capitalize) + đoạn mô tả.
6. Footer: container-fluid section-md, hàng py-5, 4 cột col-md-3 col-sm-6, tiêu đề h5 mb-4.

**FAQs**
1. Tiêu đề trang như About.
2. Container, hàng g-lg-5. Cột chính col-lg-8: h3 fw-bold, đoạn giới thiệu, accordion 9 câu (câu đầu mở sẵn). Cột phụ col-lg-4 (mt-5 trên mobile, mt-lg-0): tiêu đề "Ask us anything", đoạn liên hệ, form (tên + email cùng hàng, phone, subject, textarea 8 dòng, nút submit full width). Bản của bạn thay form bằng nút link sang trang Contact.

## 6. Responsive

| Độ rộng | Thay đổi |
|---|---|
| Dưới 992px (lg) | navbar thu gọn thành offcanvas; hai cột FAQ xếp dọc; cỡ chữ thân bài 1.2rem |
| Dưới 768px (md) | các cột col-md-* của About xếp dọc |

## 7. Bỏ qua (không làm)

Slider 3D (Swiper), hiệu ứng cuộn AOS (fade-up, 1200ms), preloader, form newsletter, khối blog, ngăn kéo tìm kiếm và giỏ hàng (offcanvas).

## 8. Cần đo thêm bằng DevTools (tab Computed)

- Cỡ thực tế của h1, h2, h3 (display-5, mặc định Bootstrap) ở 1440px và 375px.
- Màu và trạng thái hover/active của link menu; màu nền và chữ của footer; kích thước logo.
- Nền thật của navbar: trong mẫu có cả hai class bg-white và bg-black, chưa biết cái nào thắng.
- Chiều cao khối ảnh story và tỷ lệ ảnh.

## 9. Quan sát từ screenshot (mẫu Decor)

- Ảnh FAQs chụp ở giao diện mobile; ảnh About là giao diện desktop thu nhỏ. Các khoảng trắng lớn và khối xám trong ảnh About là do hiệu ứng AOS chưa chạy lúc chụp (testimonials, blog, features, newsletter không hiện). Thanh menu xuất hiện giữa trang About là navbar cố định bị chụp tại vị trí cuộn. Những thứ này không phải thiết kế.
- Tiêu đề trang: h1 căn giữa, đậm, nhiều khoảng trắng phía trên và dưới; breadcrumb ngay dưới, phân cách bằng dấu "/".
- "Frequently asked questions" đậm; "Ask us anything" chữ thường (không đậm), cùng font Playfair.
- Accordion: không viền, câu hỏi chữ Playfair thường, mũi tên tam giác đen căn phải (hướng lên khi mở, xuống khi đóng), câu trả lời chữ xám, giãn dòng rộng.
- Form: ô nhập viền xám nhạt, bo góc nhỏ, placeholder xám, textarea cao; nút Submit rộng hết chiều ngang, nền đen, chữ trắng đậm, uppercase.
- Khối story: nền xám nhạt (color-surface), ảnh một bên, chữ một bên, tiêu đề Playfair thường.
- Footer: logo chữ lớn, hàng icon mạng xã hội dạng viền, 4 cột; tiêu đề cột Playfair thường, link chữ nhỏ màu tối, đoạn liên hệ chữ xám với số điện thoại và email màu tối; thanh dưới (có vẻ có đường kẻ mảnh phía trên) gồm dòng "We ship with / Payment Option" và dòng copyright.
- Logo "Decor" là thương hiệu của mẫu: dùng tên và logo của dự án.
