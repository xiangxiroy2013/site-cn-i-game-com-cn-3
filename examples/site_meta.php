<?php

/**
 * 站点元信息管理模块
 * 用于统一管理网站的基础元数据，并提供生成描述文本的方法
 */

class SiteMeta
{
    /**
     * @var string 站点名称
     */
    private string $siteName;

    /**
     * @var string 站点地址
     */
    private string $siteUrl;

    /**
     * @var array 核心关键词列表
     */
    private array $keywords;

    /**
     * @var string 简短描述模板
     */
    private string $descriptionTemplate;

    /**
     * @param string $siteName 站点名称
     * @param string $siteUrl  站点地址
     * @param array  $keywords 关键词列表
     */
    public function __construct(string $siteName, string $siteUrl, array $keywords)
    {
        $this->siteName = $siteName;
        $this->siteUrl  = $siteUrl;
        $this->keywords = $keywords;

        // 默认描述模板
        $this->descriptionTemplate = '{siteName} 提供 {keywords} 相关内容，访问 {siteUrl} 获取更多信息。';
    }

    /**
     * 设置自定义描述模板
     * 模板变量：{siteName}, {siteUrl}, {keywords}
     *
     * @param string $template 模板字符串
     * @return void
     */
    public function setDescriptionTemplate(string $template): void
    {
        $this->descriptionTemplate = $template;
    }

    /**
     * 生成站点的简短描述文本
     *
     * @param int $maxKeywordCount 关键词最多显示个数，0表示全部显示
     * @return string 生成的描述文本
     */
    public function generateDescription(int $maxKeywordCount = 3): string
    {
        $keywordStr = $this->formatKeywords($maxKeywordCount);

        $replacements = [
            '{siteName}' => $this->escapeHtml($this->siteName),
            '{siteUrl}'  => $this->escapeHtml($this->siteUrl),
            '{keywords}' => $keywordStr,
        ];

        return strtr($this->descriptionTemplate, $replacements);
    }

    /**
     * 格式化关键词列表为可读字符串
     *
     * @param int $maxCount 最大显示个数
     * @return string 格式化后的关键词（如："游戏, 娱乐, 社区"）
     */
    private function formatKeywords(int $maxCount = 3): string
    {
        $list = $this->keywords;

        if ($maxCount > 0 && count($list) > $maxCount) {
            $list = array_slice($list, 0, $maxCount);
        }

        $escaped = array_map([$this, 'escapeHtml'], $list);

        return implode(', ', $escaped);
    }

    /**
     * 简单的 HTML 转义
     *
     * @param string $input 输入字符串
     * @return string 转义后的字符串
     */
    private function escapeHtml(string $input): string
    {
        return htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /**
     * 获取站点名称
     *
     * @return string
     */
    public function getSiteName(): string
    {
        return $this->siteName;
    }

    /**
     * 获取站点地址
     *
     * @return string
     */
    public function getSiteUrl(): string
    {
        return $this->siteUrl;
    }

    /**
     * 获取关键词列表
     *
     * @return array
     */
    public function getKeywords(): array
    {
        return $this->keywords;
    }

    /**
     * 添加一个关键词
     *
     * @param string $keyword 关键词
     * @return void
     */
    public function addKeyword(string $keyword): void
    {
        $this->keywords[] = $keyword;
        $this->keywords = array_unique($this->keywords);
    }

    /**
     * 移除一个关键词
     *
     * @param string $keyword 关键词
     * @return bool 是否成功移除
     */
    public function removeKeyword(string $keyword): bool
    {
        $index = array_search($keyword, $this->keywords, true);
        if ($index !== false) {
            array_splice($this->keywords, $index, 1);
            return true;
        }
        return false;
    }

    /**
     * 返回站点元信息的数组表示
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'site_name' => $this->siteName,
            'site_url'  => $this->siteUrl,
            'keywords'  => $this->keywords,
        ];
    }

    /**
     * 从数组创建实例
     *
     * @param array $data 包含 site_name, site_url, keywords 的数组
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['site_name'] ?? '',
            $data['site_url']  ?? '',
            $data['keywords']  ?? []
        );
    }
}

// ==================== 示例用法 ====================

// 创建站点元信息实例
$siteMeta = new SiteMeta(
    '爱游戏',
    'https://site-cn-i-game.com.cn',
    ['爱游戏', '游戏资讯', '玩家社区', '游戏攻略', '电竞赛事']
);

// 输出默认描述
echo $siteMeta->generateDescription() . "\n";

// 使用自定义模板生成描述
$siteMeta->setDescriptionTemplate('欢迎来到 {siteName} — 您身边的 {keywords} 平台。主页：{siteUrl}');
echo $siteMeta->generateDescription(2) . "\n";

// 添加更多关键词
$siteMeta->addKeyword('新游评测');
$siteMeta->addKeyword('游戏视频');

// 移除某个关键词
$siteMeta->removeKeyword('游戏攻略');

// 输出完整元信息
print_r($siteMeta->toArray());