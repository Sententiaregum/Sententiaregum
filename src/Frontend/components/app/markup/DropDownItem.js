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
import MenuItem from 'react-bootstrap/lib/MenuItem';

/**
 * Renders a dropdown component with advanced properties such as selection handlers.
 *
 * @param {Object} props Object properties.
 * @returns {React.Element} Markup.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
const DropDownItem = props => {
  return (
    <MenuItem
      eventKey={props.id}
      className={props.isActive ? 'active' : null}
      onSelect={props.onSelect}
      id={props.id}
    >
      {props.displayName}
    </MenuItem>
  );
};

DropDownItem.propTypes = {
  isActive:    React.PropTypes.bool,
  displayName: React.PropTypes.string,
  onSelect:    React.PropTypes.func,
  id:          React.PropTypes.string
};

export default DropDownItem;
