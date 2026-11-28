<?php

declare(strict_types=1);

class DashboardController extends Controller
{
    private User $userModel;
    private Post $postModel;
    private Comment $commentModel;

    public function __construct(
        ?User $userModel = null,
        ?Post $postModel = null,
        ?Comment $commentModel = null
    ) {
        require_admin();
        $this->userModel = $userModel ?? new User();
        $this->postModel = $postModel ?? new Post();
        $this->commentModel = $commentModel ?? new Comment();
    }

    public function index(): void
    {
        // CHANGED: Nạp thống kê thật từ Post & Comment thay cho con số cứng trên Dashboard
        $postTotal = $this->postModel->adminCount('', null);
        $postPublished = $this->postModel->adminCount('', 'published');
        $postDraft = $this->postModel->adminCount('', 'draft');

        $commentTotal = $this->commentModel->adminCount(null);
        $commentApproved = $this->commentModel->adminCount('approved');
        $commentPending = $this->commentModel->adminCount('pending');
        $commentHidden = $this->commentModel->adminCount('hidden');

        $this->view('admin/dashboard', [
            'pageTitle'      => 'Tổng quan Quản trị | Decor Admin',
            'activeMenu'     => 'dashboard',
            'breadcrumb'     => 'Tổng quan Quản trị',
            'roleCounts'     => $this->userModel->countByRole(),
            'postStats'      => [
                'total'     => $postTotal,
                'published' => $postPublished,
                'draft'     => $postDraft,
            ],
            'commentStats'   => [
                'total'    => $commentTotal,
                'approved' => $commentApproved,
                'pending'  => $commentPending,
                'hidden'   => $commentHidden,
            ],
            'moderationOn'   => $this->commentModel->isModerationEnabled(),
            'recentPosts'    => $this->postModel->adminPaginate('', null, 5, 0),
            'recentComments' => $this->commentModel->adminPaginate(null, null, '', 4, 0),
        ], 'layouts/admin');
    }

    public function roles(): void
    {
        $this->view('admin/roles', [
            'pageTitle'   => 'Vai trò & quyền hạn | Decor Admin',
            'activeMenu'  => 'roles',
            'breadcrumb'  => 'Vai trò & quyền hạn',
            'roleCounts'  => $this->userModel->countByRole(),
            'pageScripts' => ['js/admin-roles.js'],
        ], 'layouts/admin');
    }
}
