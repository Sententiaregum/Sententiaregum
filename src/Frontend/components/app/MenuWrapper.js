/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian.bosch.27@gmail.com>
 * (c) Ben Bieler <benjaminbieler2014@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

'use strict';

import Navbar from 'react-bootstrap/lib/Navbar';
import React from 'react';
import LanguageSwitcher from './widgets/LanguageSwitcher';
import Nav from 'react-bootstrap/lib/Nav';

/**
 * Wrapper class for menu.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
export default class MenuWrapper extends React.Component {
  /**
   * Renders the wrapper of the menu.
   *
   * @returns {React.Element} Basic menu rendering markup.
   */
  render() {
    return (
      <Navbar inverse fixedTop>
        <Navbar.Header>
          <Navbar.Brand>
            <a href="/#/">Sententiaregum</a>
          </Navbar.Brand>
          <Navbar.Toggle />
        </Navbar.Header>
        <Navbar.Collapse>
          <Nav>
            <LanguageSwitcher />
          </Nav>
          {this.props.children}
        </Navbar.Collapse>
      </Navbar>
    );
  }
}

MenuWrapper.propTypes = {
  children: React.PropTypes.node
};
