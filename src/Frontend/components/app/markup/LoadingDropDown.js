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
import Translate from 'react-translate-component';
import MenuItem from 'react-bootstrap/lib/MenuItem';

/**
 * Simple react component that renders a dropdown that indicates initialization of the dropdown.
 *
 * @param {Object.<string>} props Properties of the component.
 * @returns {React.Element} Markup of the dropdown item.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
const LoadingDropDown = props => {
  return (
    <MenuItem eventKey="1.1">
      <span className="loading">
        <Translate content={props.translationContent} />
      </span>
    </MenuItem>
  );
};

LoadingDropDown.propTypes = {
  translationContent: React.PropTypes.string
};

export default LoadingDropDown;
