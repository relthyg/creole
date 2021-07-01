<?php
/*
 * $Id: MySQLiResultSetTest.php,v 1.1 2004/09/17 17:41:59 sb Exp $
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

require_once 'creole/ResultSetTest.php';

/**
 * Tests for MySQLiResultSet.
 *
 * @author Sebastian Bergmann <sb@sebastian-bergmann.de>
 *
 * @version $Revision: 1.1 $
 */
class MySQLiResultSetTest extends ResultSetTest
{
    /**
     * Unfortunatley MySQL always applies rtrim() on strings ....
     *
     * @test
     */
    public function untrimmedGet()
    {
        $str = 'TEST    ';

        $exch = DriverTestManager::getExchange('ResultSetTest.setString.RTRIM');
        $stmt = $this->conn->prepareStatement($exch->getSql());
        $stmt->setString(1, $str);
        $stmt->setInt(2, 1);
        $stmt->executeUpdate();
        $stmt->close();

        $exch = DriverTestManager::getExchange('ResultSetTest.getString.RTRIM');
        $stmt = $this->conn->prepareStatement($exch->getSql());
        $stmt->setInt(1, 1);
        $rs = $stmt->executeQuery(ResultSet::FETCHMODE_NUM);
        $rs->next();
        $this->assertEquals(rtrim($str), $rs->getString(1));

        $stmt->close();
        $rs->close();
    }
}
