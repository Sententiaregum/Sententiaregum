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
import Row from 'react-bootstrap/lib/Row';
import Col from 'react-bootstrap/lib/Col';
import Grid from 'react-bootstrap/lib/Grid';
import invariant from 'invariant';

/**
 * Wrapper component for the inversed grid.
 *
 * @param {Object} props The component properties.
 *
 * @returns {React.Element} The markup.
 */
export default props => {
  invariant(
    2 === props.children.length,
    'This element requires exactly 2 children!'
  );

  return (
    <Grid className="container-without-padding">
      <Row className="inversed-column-container">
        <Col md={6} className="grid-item-1">
          {props.children[0]}
        </Col>
        <Col md={6} className="grid-item-2">
          {props.children[1]}
        </Col>
      </Row>
    </Grid>
  );
};
