<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" 
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:media="http://search.yahoo.com/mrss/">
    
    <xsl:output method="html" indent="yes" doctype-system="about:legacy-compat"/>
    
  <xsl:template match="/">
  <html>
    <head>
      <title>Users Feed</title>
      <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px; 
            line-height: 1.6;
        }
        .user-profile {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .user-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
        }
        .user-info {
            flex: 1;
        }
        .user-name {
            font-size: 1.8em;
            margin: 0 0 5px 0;
            color: #2c3e50;
        }
        .user-stats {
            display: flex;
            gap: 15px;
            margin: 10px 0;
        }
        .stat {
            font-size: 0.9em;
        }
        .stat strong {
            font-size: 1.1em;
            color: #2c3e50;
        }
        .pin { 
            border: 1px solid #ddd; 
            padding: 15px; 
            margin-bottom: 20px; 
            border-radius: 8px; 
            background-color: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .pin-title { 
            color: #2c3e50; 
            font-size: 1.4em; 
            margin-bottom: 5px; 
        }
        .pin-image { 
            max-width: 100%; 
            max-height: 30em; 
            margin: 10px 0; 
            border-radius: 4px;
        }
        .pin-meta { 
            color: #7f8c8d; 
            font-size: 0.9em; 
            margin-bottom: 10px;
        }
        .categories { 
            margin-top: 10px; 
        }
        .category { 
            display: inline-block; 
            background: #f0f0f0; 
            padding: 3px 8px; 
            margin-right: 5px; 
            border-radius: 3px; 
            font-size: 0.8em;
        }
        .pin-stats {
            font-size: 0.9em;
            color: #7f8c8d;
            margin-top: 10px;
        }
        .pin-description {
            margin: 10px 0;
            line-height: 1.5;
        }
        .header {
            margin-bottom: 20px;
        }
        .generated-date {
            color: #7f8c8d;
            font-size: 0.9em;
        }
        .pagination {
            display: flex !important;
            list-style: none;
            padding: 0;
            justify-content: center;
            margin: 20px 0;
            font-family: Arial, sans-serif;
          }
          .page-item {
            margin: 0 5px;
          }
          .page-link {
            display: block;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-decoration: none;
            color: #333;
            background: #fff;
            transition: all 0.3s ease;
          }
          .page-link:hover {
            background-color: #f0f0f0;
          }
          .active .page-link {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
          }
          .disabled .page-link {
            color: #6c757d;
            pointer-events: none;
            background-color: #fff;
            border-color: #ddd;
          }
          .page-link-ends {
            font-weight: bold;
          }
      </style>
    </head>
    <body>
      <h1>Users Feed</h1>
      <p>Generated on: <xsl:value-of select="pins/metadata/generated"/></p>

      <h1 class="info">
        <xsl:value-of select="pins/info"/>
      </h1>
      <div class="user-profile">
        <xsl:if test="pins/user_profile/avatar">
          <img class="user-avatar" src="{pins/user_profile/avatar}" alt="{pins/user_profile/name}"/>
        </xsl:if>
        <div class="user-info">
          <h2 class="user-name">
            <xsl:value-of select="pins/user_profile/name"/>
          </h2>
          
          <xsl:if test="pins/user_profile/bio">
            <div class="user-bio">
              <xsl:value-of select="pins/user_profile/bio" disable-output-escaping="yes"/>
            </div>
          </xsl:if>
          
          <div class="user-stats">
            <div class="stat">
              <strong><xsl:value-of select="pins/user_profile/stats/posts_count"/></strong> posts
            </div>
            <xsl:if test="pins/user_profile/stats/likes">
              <div class="stat">
                <strong><xsl:value-of select="pins/user_profile/stats/likes"/></strong> likes
              </div>
            </xsl:if>
            <xsl:if test="pins/user_profile/stats/comments">
              <div class="stat">
                <strong><xsl:value-of select="pins/user_profile/stats/comments"/></strong> Comments
              </div>
            </xsl:if>
          </div>
          
          <a href="{pins/user_profile/profile_url}" class="profile-link">View full profile</a>
        </div>
      </div>



      <div class="pin-container">
        <xsl:for-each select="pins/pin">
          <div class="pin">
            <h2 class="pin-title"><xsl:value-of select="title"/></h2>
            <div class="pin-meta">
              Posted by <a href="{author/profile}"><xsl:value-of select="author/name"/></a> on 
              <xsl:value-of select="substring(created, 0, 11)"/>
            </div>
            
            <xsl:if test="image/url">
              <img class="pin-image" src="{image/url}" alt="{title}"/>
            </xsl:if>
            
            <div class="pin-description">
              <xsl:value-of select="description" disable-output-escaping="yes"/>
            </div>
            
            <div class="pin-stats">
              Likes: <xsl:value-of select="stats/likes"/> | 
              Comments: <xsl:value-of select="stats/comments"/>
            </div>
            
            <xsl:if test="categories/category">
              <div class="categories">
                <xsl:for-each select="categories/category">
                  <span class="category"><xsl:value-of select="."/></span>
                </xsl:for-each>
              </div>
            </xsl:if>
          </div>
        </xsl:for-each>
      </div>
      <xsl:apply-templates select="pins/pagination"/>
    </body>
  </html>
</xsl:template>

<xsl:template match="pagination">
    <nav aria-label="Page navigation">
      <ul class="pagination">
        <!-- First Page Link -->
        <li class="page-item">
          <a>
            <xsl:attribute name="href">
              <xsl:value-of select="normalize-space(first)"/>
            </xsl:attribute>
            <xsl:attribute name="class">page-link page-link-ends</xsl:attribute>
            <xsl:attribute name="title">First Page</xsl:attribute>
            « First
          </a>
        </li>
        
        <!-- Previous Page Link -->
        <li class="page-item">
          <xsl:if test="contains(normalize-space(previous), 'page=1')">
            <xsl:attribute name="class">page-item disabled</xsl:attribute>
          </xsl:if>
          <a>
            <xsl:attribute name="href">
              <xsl:value-of select="normalize-space(previous)"/>
            </xsl:attribute>
            <xsl:attribute name="class">page-link page-link-ends</xsl:attribute>
            <xsl:attribute name="title">Previous Page</xsl:attribute>
            ‹ Prev
          </a>
        </li>
        
        <!-- Page Numbers -->
        <xsl:for-each select="pages/page">
          <li class="page-item">
            <xsl:if test="@active='true'">
              <xsl:attribute name="class">page-item active</xsl:attribute>
            </xsl:if>
            <a>
              <xsl:attribute name="href">
                <xsl:value-of select="normalize-space(url)"/>
              </xsl:attribute>
              <xsl:attribute name="class">page-link</xsl:attribute>
              <xsl:value-of select="number"/>
            </a>
          </li>
        </xsl:for-each>
        
        <!-- Next Page Link -->
        <li class="page-item">
          <a>
            <xsl:attribute name="href">
              <xsl:value-of select="normalize-space(next)"/>
            </xsl:attribute>
            <xsl:attribute name="class">page-link page-link-ends</xsl:attribute>
            <xsl:attribute name="title">Next Page</xsl:attribute>
            Next ›
          </a>
        </li>
        
        <!-- Last Page Link -->
        <li class="page-item">
          <a>
            <xsl:attribute name="href">
              <xsl:value-of select="normalize-space(last)"/>
            </xsl:attribute>
            <xsl:attribute name="class">page-link page-link-ends</xsl:attribute>
            <xsl:attribute name="title">Last Page</xsl:attribute>
            Last »
          </a>
        </li>
      </ul>
    </nav>
    
    <!-- Current Page Info -->
    <div style="text-align: center; margin-top: 10px;">
      <xsl:for-each select="pages/page[@active='true']">
        <span>Page <strong><xsl:value-of select="number"/></strong> of <strong><xsl:value-of select="count(../page)"/></strong></span>
      </xsl:for-each>
    </div>
</xsl:template>

</xsl:stylesheet>