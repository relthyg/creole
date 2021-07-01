<?php
/*
 *  $Id: Lob.php,v 1.10 2004/03/20 04:16:50 hlellelid Exp $
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information please see
 * <http://creole.phpdb.org>.
 */

/**
 * An abstract class for handling LOB (Locator Object) columns.
 *
 * @author    Hans Lellelid <hans@xmpl.org>
 *
 * @version   $Revision: 1.10 $
 */
abstract class Lob
{
    /**
     * The contents of the Lob.
     * DO NOT SET DIRECTLY (or you will disrupt the
     * ability of isModified() to give accurate results).
     *
     * @var string
     */
    protected $data;

    /**
     * File that blob should be written out to.
     *
     * @var string
     */
    protected $outFile;

    /**
     * File that blob should be read in from.
     *
     * @var string
     */
    protected $inFile;

    /**
     * This is a 3-state value indicating whether column has been
     * modified.
     * Initially it is NULL.  Once first call to setContents() is made
     * it is FALSE, because this will be initial state of Lob.  Once
     * a subsequent call to setContents() is made it is TRUE.
     *
     * @var bool
     */
    private $modified = null;

    /**
     * Construct a new Lob.
     *
     * @param sttring $data The data contents of the Lob.
     *
     * @see setContents()
     */
    public function __construct($data = null)
    {
        if (null !== $data) {
            $this->setContents($data);
        }
    }

    /**
     * Get the contents of the LOB.
     *
     * @return string The characters in this LOB.
     *
     * @throws Exception
     */
    public function getContents()
    {
        if (null === $this->data && $this->isFromFile()) {
            $this->readFromFile();
        }

        return $this->data;
    }

    /**
     * Set the contents of this LOB.
     * Sets the modified flag to FALSE if this is the first call
     * to setContents() for this object.  Sets the bit to TRUE if
     * this any subsequent call to setContents().
     *
     * @param string $bytes
     */
    public function setContents($data)
    {
        $this->data = $data;

        if (null === $this->modified) {
            // if modified bit hasn't been set yet,
            // then it should now be set to FALSE, since
            // we just did inital population
            $this->modified = false;
        } elseif (false === $this->modified) {
            // if it was already FALSE, then it should
            // now be set to TRUE, since this is a subsequent
            // modfiication.
            $this->modified = true;
        }
    }

    /**
     * Dump the contents of the file to stdout.
     * Must be implemented by subclasses so that binary status is handled
     * correctly. (i.e. ignored for Clob, handled for Blob).
     *
     * @return void
     *
     * @throws Exception if no file or contents.
     */
    abstract public function dump();

    /**
     * Specify the file that we want this LOB read from.
     *
     * @param string $filePath The location of the file.
     *
     * @return void
     */
    public function setInputFile($filePath)
    {
        $this->inFile = $filePath;
    }

    /**
     * Get the file that we want this LOB read from.
     *
     * @return string The location of the file.
     */
    public function getInputFile()
    {
        return $this->inFile;
    }

    /**
     * Specify the file that we want this LOB saved to.
     *
     * @param string $filePath The location of the file.
     *
     * @return void
     */
    public function setOutputFile($filePath)
    {
        $this->outFile = $filePath;
    }

    /**
     * Get the file that we want this LOB saved to.
     *
     * @return string $filePath The location of the file.
     */
    public function getOutputFile()
    {
        return $this->outFile;
    }

    /**
     * Returns whether this Lob is loaded from file.
     * This is useful for bypassing need to read in the contents of the Lob.
     *
     * @return bool Whether this LOB is to be read from a file.
     */
    public function isFromFile()
    {
        return null !== $this->inFile;
    }

    /**
     * Read LOB data from file (binary safe).
     * (Implementation may need to be moved into Clob / Blob subclasses, but
     * since file_get_contents() is binary-safe, it hasn't been necessary so far.).
     *
     * @param string $file Filename may also be specified here (if not specified using setInputFile()).
     *
     * @return void
     *
     * @throws Exception - if no file specified or error on read.
     *
     * @see setInputFile()
     */
    public function readFromFile($file = null)
    {
        if (null !== $file) {
            $this->setInputFile($file);
        }
        if (!$this->inFile) {
            throw Exception('No file specified for read.');
        }
        $data = @file_get_contents($this->inFile);
        if (false === $data) {
            throw new Exception('Unable to read from file: '.$this->inFile);
        }
        $this->setContents($data);
    }

    /**
     * Write LOB data to file (binary safe).
     * (Impl may need to move into subclasses, but so far not necessary.).
     *
     * @param string $file Filename may also be specified here (if not set using setOutputFile()).
     *
     * @throws Exception - if no file specified, no contents to write, or error on write.
     *
     * @see setOutputFile()
     */
    public function writeToFile($file = null)
    {
        if (null !== $file) {
            $this->setOutputFile($file);
        }
        if (!$this->outFile) {
            throw new Exception('No file specified for write');
        }
        if (null === $this->data) {
            throw new Exception('No data to write to file');
        }
        if (false === @file_put_contents($this->outFile, $this->data)) {
            throw new Exception('Unable to write to file: '.$this->outFile);
        }
    }

    /**
     * Convenience method to get contents of LOB as string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getContents();
    }

    /**
     * Set whether LOB contents have been modified after initial setting.
     *
     * @param bool $b
     */
    public function setModified($b)
    {
        $this->modified = $b;
    }

    /**
     * Whether LOB contents have been modified after initial setting.
     *
     * @return bool TRUE if the contents have been modified after initial setting.
     *              FALSE if contents have not been modified or if no contents have bene set.
     */
    public function isModified()
    {
        // cast it so that NULL will also eval to false
        return (bool) $this->modified;
    }
}
