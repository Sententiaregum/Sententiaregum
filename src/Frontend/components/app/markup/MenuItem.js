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

import React from 'react';
import NavItem from 'react-bootstrap/lib/NavItem';
import Translate from 'react-translate-component';

/**
 * Simple markup component for a menu item.
 *
 * @param {Object} props The properties passed to the component.
 *
 * @returns {React.Element} The markup for the menu component.
 */
const MenuItem = props => {
  return (
    <NavItem href={props.url}>
      <Translate content={props.label} />
    </NavItem>
  );
};

MenuItem.PropTypes = {
  url:   React.PropTypes.string,
  label: React.PropTypes.string
};

export default MenuItem;
