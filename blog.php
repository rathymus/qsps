<?php
	$GLOBAL_PAGE="blog";

	include_once 'header.php';
//Uncomment to enable the NBBC bbcode parser
//	include_once 'nbbc.php';
	
	class BlogEntry{
		var $snail;
		var $title;
		var $created;
		var $lastmod;
		
		public function __construct($line){
			list($this->snail, $this->title, $this->created, $this->lastmod) = explode("|", $line, 4);
		}
		public function getTitle(){return $this->title;}
		
		public function getSnail(){return $this->snail;}
		public function getDateCreated(){ return $this->created;}
		public function getLastModified() {return $this->lastmod;}
		
		public function displayBlogEntry(){
			$contents=@file_get_contents(SITE_BLOG.$this->snail);
			if($contents==null) return false;
			//if nbbc is available
			/*
			$bbcode= new BBCode;
			print $bbcode->Parse($contents);
			*/
			//else
			echo $contents;
			
		}
	}
	
	class Blog implements Countable{
		private $numEntries;
		private $entries;
		
		public function __construct(){
			$contents=@file_get_contents(SITE_BLOG.SITE_BLOG_INDEX);
			if($contents!=NULL){
				$contents=str_replace("\n\r", "\n", $contents);
				$contents=explode("\n", $contents);
				$this->numEntries=count($contents);
				//create the container
				$entries=new SplFixedArray($this->numEntries);
				for($i=0; $i<$this->numEntries; $i++)
					$this->entries[$i]=new BlogEntry($contents[$i]);
			}else{
			//	echo "No blog posts at this time.</br>";
			}
			
		}
		
		public function displayHeadersList($limit=0){
			if($limit==0 || $limit>$this->numEntries) $limit=$this->numEntries;
			if($limit==0){
				echo "Blog is currently empty.";
				return;
			}
			echo "<table>";
			for($i=0; $i<$limit ; $i++){
				$tmp=$this->entries[$i];
				echo '<tr  class=\"blog_table\"><td  class=\"blog_table\">'.$tmp->getDateCreated().'</td><td  class=\"blog_table\"><a href="'.SITE_URL.'blog.php?post='.
				$tmp->getSnail()
				.'">'.$tmp->getTitle().'</a></td></tr>';
			}
			echo "</table>"; 
		}

		public function displayPost($snail){
			$tmp=null;
			$prev=null;
			$next=null;
			for($i=0; $i<$this->numEntries; $i++){
				$tmp=$this->entries[$i];
				if($tmp->getSnail()===$snail){
					if($i<$this->numEntries) $next=$this->entries[++$i];
					include_once 'header.php';
					echo_header(SELECT_BLOG);
					echo "<h2>".$tmp->getTitle()."</h2></br><i>Date Created: </i>".$tmp->getDateCreated()."</br>";
					if($prev!=NULL) echo "Previous: <a href=\"blog.php?post=".$prev->getSnail()."\">".$prev->getTitle()."</a></br>";
					if($next!=NULL) echo "Next: <a href=\"blog.php?post=".$next->getSnail()."\">".$next->getTitle()."</a></br>";
					echo " Back to<a href=\"blog.php\">Index</a><p>";
					$tmp->displayBlogEntry();
					echo "</p>";
					echo "</br></br>";

					echo_footer();
					return;
				}else $prev=$tmp;
			}
			echo "404 error!";
		}
		public function count() {return $this->numEntries;}
		
	}

	$blog=new Blog;
	
	//See if GET has any parameters
	if(!isset($_GET["post"])){
		echo_header(SELECT_BLOG);
		echo "<p>Blog entries, most recent first</p>";
		$blog->displayHeadersList(0);
	}else
		$blog->displayPost($_GET["post"]);
		
	echo_footer();

?>